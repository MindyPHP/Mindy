<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 27/05/14.05.2014 16:43
 */

namespace Mindy\Orm\Fields;


use Mindy\Helper\Meta;
use Mindy\Orm\Traits\UniqueUrl;
use Mindy\Query\Expression;

class AutoSlugField extends CharField
{
    use UniqueUrl;
    /**
     * @var string
     */
    public $source = 'name';
    /**
     * @var bool
     */
    public $autoFetch = true;
    /**
     * @var string|null
     */
    protected $oldValue;

    
    public function onBeforeInsert()
    {
        $model = $this->getModel();
        $value = empty($this->value) ? Meta::cleanString($model->{$this->source}) : ltrim($this->value, '/');
        if($model->parent) {
            $url = $model->parent->{$this->name} . '/' . $value;
        } else {
            $url = $value;
        }

        $url = ltrim($url, '/');
        if ($this->unique) {
            $url = $this->uniqueUrl($url);
        }

        $model->setAttribute($this->name, $url);
    }

    public function onBeforeUpdate()
    {
        /** @var $model \Mindy\Orm\TreeModel */
        $model = $this->getModel();

        // Случай когда обнулен slug, например из админки
        if(empty($model->{$this->name})) {
            $model->{$this->name} = Meta::cleanString($model->{$this->source});
        }

        // if remove parent (parent is null)
        if (!$model->parent) {
            if(strpos($model->{$this->name}, '/') === false) {
                $url = Meta::cleanString($model->{$this->name});
            } else {
                if($model->{$this->name}) {
                    $slugs = explode('/', $model->{$this->name});
                    $url = end($slugs);
                } else {
                    $url = Meta::cleanString($model->{$this->source});
                }
            }
        } else {
            $parentUrl = $model->parent->{$this->name};
            $slugs = explode('/', $model->{$this->name});
            $url = $parentUrl . '/' . end($slugs);
        }

        $url = ltrim($url, '/');
        if ($this->unique) {
            $url = $this->uniqueUrl($url, 0, $model->pk);
        }

        $model->setAttribute($this->name, $url);

        $model->tree()->filter([
            'lft__gt' => $model->getOldAttribute('lft'),
            'rgt__lt' => $model->getOldAttribute('rgt'),
            'root' => $model->getOldAttribute('root')
        ])->update([
            $this->name => new Expression("REPLACE(`{$this->name}`, '{$model->getOldAttribute($this->name)}', '{$url}')")
        ]);
    }

    public function getFormValue()
    {
        $slugs = explode('/', $this->getValue());
        return end($slugs);
    }

    public function getFormField($form, $fieldClass = null, array $extra = [])
    {
        return parent::getFormField($form, \Mindy\Form\Fields\ShortUrlField::className(), $extra);
    }
}
