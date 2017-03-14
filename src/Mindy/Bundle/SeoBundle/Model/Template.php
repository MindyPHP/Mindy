<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\SeoBundle\Model;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Model;

/**
 * Class Template.
 *
 * @property string $code
 * @property string $content
 */
class Template extends Model
{
    public static function getFields()
    {
        return [
            'code' => [
                'class' => CharField::class,
            ],
            'content' => [
                'class' => TextField::class,
            ],
        ];
    }

    public function __toString()
    {
        return (string) $this->code;
    }
}
