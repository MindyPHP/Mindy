<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/09/16
 * Time: 15:21
 */

namespace Mindy\Orm\Image;

use Mindy\Orm\Traits\FilesystemAwareTrait;

abstract class AbstractProcessor
{
    use FilesystemAwareTrait;

    /**
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        return $this->getFilesystem()->has($path);
    }

    /**
     * @param string $path
     * @return false|string
     */
    public function read($path)
    {
        return $this->getFilesystem()->read($path);
    }

    /**
     * @param $path
     * @param $contents
     * @return bool
     */
    public function write($path, $contents)
    {
        return $this->getFilesystem()->write($path, $contents);
    }
}