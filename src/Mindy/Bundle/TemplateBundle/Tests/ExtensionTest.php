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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
        $this->container->setParameter('kernel.cache_dir', __DIR__.'/cache');
        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->registerExtension($this->extension);
    }

    public function testServices()
    {
        $loader = new XmlFileLoader(
            $this->container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $engine = $this->container->get('template');
        $this->assertInstanceOf('Mindy\Template\Renderer', $engine);

        $defaultFinder = $this->container->get('template.finder.templates');
        $this->assertInstanceOf('Mindy\Finder\TemplateFinder', $defaultFinder);
    }

    public function testCompile()
    {
        $loader = new XmlFileLoader(
            $this->container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $this->container->compile();
    }
}
