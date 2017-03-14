<?php
/**
 * Created by IntelliJ IDEA.
 * User: max
 * Date: 14/03/2017
 * Time: 20:00
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
        while ($filesystem->has($uploadTo . '/' . $resolvedName)) {
            ++$i;
            $resolvedName = sprintf('%s_%d.%s', $hash, $i, $ext);
        }

        return $uploadTo . '/' . $resolvedName;
    }
}
