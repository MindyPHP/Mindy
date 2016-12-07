<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 20:06
 */

namespace Mindy\Orm;

use League\Flysystem\FilesystemInterface;

class OrmFile
{
    protected static $filesystem;

    public static function setFilesystem(FilesystemInterface $filesystem)
    {
        self::$filesystem = $filesystem;
    }

    public static function getFilesystem()
    {
        return self::$filesystem;
    }
}