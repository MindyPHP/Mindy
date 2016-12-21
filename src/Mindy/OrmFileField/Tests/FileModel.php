<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 13:30.
 */

namespace Mindy\Orm\Tests;

use Mindy\Orm\Model;

class FileModel extends Model
{
    public static function getFields()
    {
        return [];
    }

    public static function getBundleName()
    {
        return 'foo';
    }
}
