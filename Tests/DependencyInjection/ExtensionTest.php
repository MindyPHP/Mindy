<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FileBundle\Tests\DependencyInjection;

use Mindy\Bundle\FileBundle\DependencyInjection\FileExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testServices()
    {
        $services = [
            'file.template.image.library',
            'file.form.data_transformer.file',
            'file.form.extension.file_type',
        ];
        foreach ($services as $id) {
            $this->assertTrue($this->getContainer()->has($id));
        }
    }

    private function getContainer(array $options = [])
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.root_dir', 'kernel/root');
        $containerBuilder->setParameter('kernel.environment', 'test');

        (new FileExtension())->load($options, $containerBuilder);

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
