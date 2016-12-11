<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 00:07
 */

namespace Mindy\Bundle\MetaBundle\Model;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Model;

/**
 * Class Template
 * @package Mindy\Bundle\MetaBundle\Model
 * @property string $code
 * @property string $content
 */
class Template extends Model
{
    public static function getFields()
    {
        return [
            'code' => [
                'class' => CharField::class
            ],
            'content' => [
                'class' => TextField::class
            ],
        ];
    }

    public function __toString()
    {
        return (string)$this->code;
    }
}