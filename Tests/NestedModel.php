<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\OrmNestedSet\Tests;

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
