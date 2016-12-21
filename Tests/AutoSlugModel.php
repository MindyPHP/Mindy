<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 18/09/16
 * Time: 20:58.
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Fields\AutoSlugField;
use Mindy\Orm\Fields\CharField;
use Mindy\Orm\TreeModel;

class AutoSlugModel extends TreeModel
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'name' => [
                'class' => CharField::class,
            ],
            'slug' => [
                'class' => AutoSlugField::class,
            ],
        ]);
    }
}
