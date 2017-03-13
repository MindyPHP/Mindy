<?php

/*
 * This file is part of Mindy Orm.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\TreeModel;

class NestedModel extends TreeModel
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'name' => [
                'class' => CharField::class,
            ],
        ]);
    }

    public static function t($id, array $parameters = [], $domain = null, $locale = null)
    {
        return strtr($id, $parameters);
    }
}
