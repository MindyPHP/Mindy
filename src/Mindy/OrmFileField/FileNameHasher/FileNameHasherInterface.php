<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\FileNameHasher;

use League\Flysystem\FilesystemInterface;

interface FileNameHasherInterface
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function hash($name);

    /**
     * @param FilesystemInterface $filesystem
     * @param $uploadTo
     * @param $name
     *
     * @return string
     */
    public function resolveUploadPath(FilesystemInterface $filesystem, $uploadTo, $name);
}
