<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\TemplateBundle\Tests\DependencyInjection;

use Mindy\Bundle\TemplateBundle\DependencyInjection\TemplateExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateExtension
     */
    protected $extension;
    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function setUp()
    {
        $this->extension = new TemplateExtension();

        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.bundles', []);
        $this->container->setParameter('kernel.cache_dir', __DIR__.'/cache');
        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->registerExtension($this->extension);
    }

    public function testServices()
    {
        // An extension is only loaded in the container if a configuration is provided for it.
        // Then, we need to explicitely load it.
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $engine = $this->container->get('template');
        $this->assertInstanceOf('Mindy\Template\Renderer', $engine);

        $defaultFinder = $this->container->get('template.finder.templates');
        $this->assertInstanceOf('Mindy\Finder\TemplateFinder', $defaultFinder);
    }

    public function testParameters()
    {
        // An extension is only loaded in the container if a configuration is provided for it.
        // Then, we need to explicitely load it.
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertSame(0, $this->container->getParameter('mindy.template.mode'));
        $this->assertSame('templates', $this->container->getParameter('mindy.template.dir'));
        $this->assertSame('default', $this->container->getParameter('mindy.template.theme'));
        $this->assertSame(__DIR__.'/Resources', $this->container->getParameter('mindy.template.base_path'));
        $this->assertSame(__DIR__.'/cache/templates', $this->container->getParameter('mindy.template.cache_dir'));
        $this->assertTrue($this->container->getParameter('mindy.template.auto_escape'));
    }
}
