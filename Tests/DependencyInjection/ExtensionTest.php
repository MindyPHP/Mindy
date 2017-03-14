<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Tests\DependencyInjection;

use Ivory\CKEditorBundle\DependencyInjection\IvoryCKEditorExtension;
use Mindy\Bundle\CKEditorBundle\DependencyInjection\CKEditorExtension;
use Mindy\Bundle\PageBundle\DependencyInjection\PageExtension;
use Mindy\Finder\TemplateFinderInterface;
use Mindy\Template\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testServices()
    {
        $services = [
            'mindy.bundle.page.template.library',
            'mindy.bundle.page.admin.page',
            'mindy.bundle.page.template_loader',
            'mindy.bundle.page.meta_generator.page',
            'mindy.bundle.page.form.page_form',
            'mindy.bundle.page.sitemap.page',
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

        (new PageExtension())->load($options, $containerBuilder);
        (new IvoryCKEditorExtension())->load($options, $containerBuilder);
        (new CKEditorExtension())->load($options, $containerBuilder);

        $rendererMock = $this->getMockBuilder(RendererInterface::class)->getMock();
        $containerBuilder->set('templating.engine.mindy', $rendererMock);

        $chainTemplateFinder = $this->getMockBuilder(TemplateFinderInterface::class)->getMock();
        $containerBuilder->set('template.finder.chain', $chainTemplateFinder);

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
