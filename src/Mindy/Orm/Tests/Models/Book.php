<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests\Models;

use Mindy\Orm\Fields\ForeignField;
use Mindy\Orm\Model;

class Book extends Model
{
    public static function getFields()
    {
        return [
            'category' => [
                'class' => ForeignField::class,
                'modelClass' => BookCategory::class,
                'null' => true,
                'editable' => false,
            ],
            'category_new' => [
                'class' => ForeignField::class,
                'modelClass' => BookCategory::class,
                'null' => true,
                'editable' => false,
            ],
        ];
    }
}
