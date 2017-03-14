<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
