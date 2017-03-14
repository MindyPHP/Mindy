<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
