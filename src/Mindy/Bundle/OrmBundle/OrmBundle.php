<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 10/10/2016
 * Time: 01:15
 */

namespace Mindy\Bundle\OrmBundle;

use League\Flysystem\FilesystemInterface;
use Mindy\Orm\OrmFile;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrmBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $id = 'oneup_flysystem.default_filesystem';

        if ($container->has($id)) {
            /** @var FilesystemInterface $filesystem */
            $filesystem = $container->get($id);
            OrmFile::setFilesystem($filesystem);
        }
    }
}