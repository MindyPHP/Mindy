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

class DefaultHasher implements FileNameHasherInterface
{
    /**
     * {@inheritdoc}
     */
    public function hash($fileName)
    {
        return $fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveUploadPath(FilesystemInterface $filesystem, $uploadTo, $name)
    {
        $uploadTo = ltrim($uploadTo, '/');

        if (empty($name)) {
            throw new \RuntimeException('Empty file name received');
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $hash = $this->hash(pathinfo($name, PATHINFO_FILENAME));

        $i = 0;
        $resolvedName = sprintf('%s.%s', $hash, $ext);
        while ($filesystem->has($uploadTo.'/'.$resolvedName)) {
            ++$i;
            $resolvedName = sprintf('%s_%d.%s', $hash, $i, $ext);
        }

        return $uploadTo.'/'.$resolvedName;
    }
}
