<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Fields;

use Mindy\Orm\ModelInterface;
use Mindy\QueryBuilder\Expression;

/**
 * Class AutoSlugField.
 */
class AutoSlugField extends AbstractSlugField
{
    /**
     * @var string|null
     */
    protected $oldValue;

    /**
     * Internal event.
     *
     * @param \Mindy\Orm\TreeModel|ModelInterface $model
     * @param $value
     */
    public function beforeInsert(ModelInterface $model, $value)
    {
        if (empty($value)) {
            $slug = $this->createSlug($model->getAttribute($this->source));
        } else {
            $slug = $this->getLastSegment($value);
        }

        if ($model->parent) {
            $slug = $model->parent->getAttribute($this->getAttributeName()).'/'.ltrim($slug, '/');
        }

        $model->setAttribute($this->getAttributeName(), $this->uniqueUrl(ltrim($slug, '/')));
    }

    /**
     * @param $slug
     *
     * @return string
     */
    protected function getLastSegment($slug)
    {
        if (strpos($slug, '/') === false) {
            return $slug;
        }

        return substr($slug, strrpos($slug, '/', -1) + 1);
    }

    /**
     * @param $slug
     *
     * @return string
     */
    protected function getParentSegment($slug)
    {
        if (strpos($slug, '/') === false) {
            return $slug;
        }

        return substr($slug, 0, strrpos($slug, '/', -1));
    }

    /**
     * Internal event.
     *
     * @param \Mindy\Orm\TreeModel|ModelInterface $model
     * @param $value
     */
    public function beforeUpdate(ModelInterface $model, $value)
    {
        if (empty($value)) {
            $slug = $this->createSlug($model->getAttribute($this->source));
        } else {
            $slug = $this->getLastSegment($value);
        }

        if ($model->parent) {
            $slug = implode('/', [
                $this->getParentSegment($model->parent->getAttribute($this->getAttributeName())),
                $slug,
            ]);
        }

        $slug = $this->uniqueUrl(ltrim($slug, '/'), 0, $model->pk);

        $conditions = [
            'lft__gte' => $model->getAttribute('lft'),
            'rgt__lte' => $model->getAttribute('rgt'),
            'root' => $model->getAttribute('root'),
        ];

        $attributeValue = $model->getOldAttribute($this->getAttributeName());
        if (empty($attributeValue)) {
            $attributeValue = $model->getAttribute($this->getAttributeName());
        }
        $expr = 'REPLACE([['.$this->getAttributeName().']], @'.$attributeValue.'@, @'.$slug.'@)';

        $qs = $model->objects()->filter($conditions);
        $qs->update([
            $this->getAttributeName() => new Expression($expr),
        ]);

        $model->setAttribute($this->getAttributeName(), $slug);
    }
}
