<?php

namespace Mindy\Bundle\PageBundle\Model;

use Mindy\Orm\Fields\AutoSlugField;
use Mindy\Orm\Fields\BooleanField;
use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\DateTimeField;
use Mindy\Orm\Fields\ImageField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\TreeModel;

/**
 * Class Page.
 *
 * @property string $name
 * @property bool|int $is_published
 * @property int $parent_id
 * @property Page $parent
 * @property string|int $published_at
 * @property string|null $view_children
 * @property bool|int $is_index
 * @property string $view
 * @property string $url
 * @property string $sorting
 *
 * @method static \Mindy\Bundle\PageBundle\Model\PageManager objects($instance = null)
 * @method static \Mindy\Bundle\PageBundle\PageBundle getBundle()
 */
class Page extends TreeModel
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'name' => [
                'class' => CharField::class,
                'required' => true,
            ],
            'url' => [
                'class' => AutoSlugField::class,
                'source' => 'name',
                'unique' => true,
            ],
            'content' => [
                'class' => TextField::class,
                'null' => true,
            ],
            'content_short' => [
                'class' => TextField::class,
                'null' => true,
            ],
            'file' => [
                'class' => ImageField::class,
                'null' => true,
            ],
            'created_at' => [
                'class' => DateTimeField::class,
                'autoNowAdd' => true,
                'editable' => false,
            ],
            'updated_at' => [
                'class' => DateTimeField::class,
                'autoNow' => true,
                'null' => true,
                'editable' => false,
            ],
            'published_at' => [
                'class' => DateTimeField::class,
                'null' => true,
            ],
            'view' => [
                'class' => CharField::class,
                'null' => true,
            ],
            'view_children' => [
                'class' => CharField::class,
                'null' => true,
            ],
            'is_index' => [
                'class' => BooleanField::class,
            ],
            'is_published' => [
                'class' => BooleanField::class,
                'default' => true,
            ],
            'sorting' => [
                'class' => CharField::class,
                'null' => true,
                'choices' => self::getSortingChoices(),
            ],
        ]);
    }

    public static function getSortingChoices()
    {
        return [
            'published_at' => 'page.page.time_asc',
            '-published_at' => 'page.page.time_desc',
            'lft' => 'page.page.position_asc',
            '-lft' => 'page.page.position_desc',
        ];
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * Return view for this model.
     *
     * @return string
     */
    public function findView() : string
    {
        if (empty($this->view) == false) {
            return $this->view;
        }

        /** @var Page $parent */
        $parent = $this->objects()
            ->ancestors()
            ->filter([
                'view_children__isnull' => false,
                'view_children__isnt' => '',
            ])
            ->limit(1)
            ->get();
        if ($parent) {
            return $parent->view_children;
        } elseif ($this->getIsLeaf()) {
            return 'page/view.html';
        } else {
            return 'page/list.html';
        }
    }

    /**
     * Find parent views if this view is not set.
     *
     * @return bool|mixed
     */
    protected function getParentView()
    {
        /** @var Page $model */
        $model = $this->objects()
            ->filter([
                'lft__lt' => $this->lft,
                'rgt__gt' => $this->rgt,
                'root' => $this->root,
                'view_children__isnull' => false,
            ])
            ->order('-lft')
            ->limit(1)
            ->get();

        return $model ? $model->view_children : null;
    }

    /**
     * @return \Mindy\Orm\QuerySet
     */
    public function getChildrenQuerySet()
    {
        $qs = $this->objects()->published()->children();
        $qs->order([$this->sorting ? $this->sorting : '-published_at']);

        return $qs;
    }

    /**
     * @param Page $owner
     * @param bool $isNew
     */
    public function beforeSave($owner, $isNew)
    {
        if ($owner->is_index) {
            $owner->objects()->update(['is_index' => false]);
        }

        if ($owner->is_published) {
            if (empty($owner->published_at)) {
                $owner->published_at = time();
            } else {
                $owner->published_at = strtotime($owner->published_at);
            }
        }
    }
}
