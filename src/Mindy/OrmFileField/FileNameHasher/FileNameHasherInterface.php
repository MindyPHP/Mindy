<?php
/**
 * Created by IntelliJ IDEA.
 * User: max
 * Date: 14/03/2017
 * Time: 20:10
 */

namespace Mindy\Orm\FileNameHasher;


use League\Flysystem\FilesystemInterface;

interface FileNameHasherInterface
{
    /**
     * @param string $name
     * @return string
     */
    public function hash($name);

    /**
     * @param FilesystemInterface $filesystem
     * @param $uploadTo
     * @param $name
     * @return string
     */
    public function resolveUploadPath(FilesystemInterface $filesystem, $uploadTo, $name);
}
