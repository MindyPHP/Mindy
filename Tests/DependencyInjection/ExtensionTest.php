<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\MenuBundle\Tests\DependencyInjection;

use Mindy\Bundle\MenuBundle\DependencyInjection\MenuExtension;
use Mindy\Template\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testServices()
    {
        $this->assertTrue($this->getContainer()->has('mindy.bundle.menu.admin.menu'));
        $this->assertTrue($this->getContainer()->has('mindy.bundle.menu.template_library.menu'));
    }

    private function getContainer(array $options = [])
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.root_dir', 'kernel/root');
        $containerBuilder->setParameter('kernel.environment', 'test');

        $extension = new MenuExtension();

        $extension->load($options, $containerBuilder);

        $rendererMock = $this->getMockBuilder(RendererInterface::class)->getMock();
        $containerBuilder->set('templating.engine.mindy', $rendererMock);

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
