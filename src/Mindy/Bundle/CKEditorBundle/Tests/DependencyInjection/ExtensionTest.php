<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\CKEditorBundle\Tests\DependencyInjection;

use Ivory\CKEditorBundle\DependencyInjection\IvoryCKEditorExtension;
use Mindy\Bundle\CKEditorBundle\DependencyInjection\CKEditorExtension;
use Mindy\Template\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testServices()
    {
        $this->assertTrue($this->getContainer()->has('mindy.bundle.ckeditor.template_library.ckeditor'));
        $this->assertTrue($this->getContainer()->has('ivory_ck_editor.renderer'));
    }

    private function getContainer(array $options = [])
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.root_dir', 'kernel/root');
        $containerBuilder->setParameter('kernel.environment', 'test');

        $extension = new IvoryCKEditorExtension();
        $extension->load($options, $containerBuilder);

        $extension = new CKEditorExtension();
        $extension->load($options, $containerBuilder);

        $rendererMock = $this->getMockBuilder(RendererInterface::class)->getMock();
        $containerBuilder->set('templating.engine.mindy', $rendererMock);

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
