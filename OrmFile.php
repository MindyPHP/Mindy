<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 20:06.
 */

namespace Mindy\Orm;

use League\Flysystem\FilesystemInterface;
use Mindy\Application\App;

/**
 * Class OrmFile.
 */
class OrmFile
{
    /**
     * @var FilesystemInterface|null
     */
    protected static $filesystem;

    /**
     * @param FilesystemInterface $filesystem
     */
    public static function setFilesystem(FilesystemInterface $filesystem)
    {
        self::$filesystem = $filesystem;
    }

    /**
     * @return FilesystemInterface|null
     */
    public static function getFilesystem()
    {
        if (null === self::$filesystem) {
            // TODO https://github.com/MindyPHP/Mindy/issues/7
            self::$filesystem = App::getInstance()->getComponent('oneup_flysystem.default_filesystem');
        }

        return self::$filesystem;
    }
}
