<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MenuBundle\Model;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\TreeModel;
use Mindy\Validation\Alphanumeric;

/**
 * Class Menu.
 *
 * @property string $slug
 * @property string $name
 * @property string $url
 */
class Menu extends TreeModel
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'slug' => [
                'class' => CharField::class,
                'null' => true,
            ],
            'name' => [
                'class' => CharField::class,
            ],
            'url' => [
                'class' => CharField::class,
                'null' => true,
            ],
        ]);
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
