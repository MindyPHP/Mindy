<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Thumb;

use League\Flysystem\FilesystemInterface;

abstract class AbstractProcessor
{
    protected $filesystem;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return FilesystemInterface|null
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->getFilesystem()->has($path);
    }

    /**
     * @param string $path
     *
     * @return false|string
     */
    public function read($path)
    {
        return $this->getFilesystem()->read($path);
    }

    /**
     * @param $path
     * @param $contents
     *
     * @return bool
     */
    public function write($path, $contents)
    {
        return $this->getFilesystem()->write($path, $contents);
    }
}
