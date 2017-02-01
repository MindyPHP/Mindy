<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\PaginationBundle\Tests;

use Mindy\Bundle\PaginationBundle\DependencyInjection\PaginationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class BundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;
    /**
     * @var PaginationExtension
     */
    protected $extension;

    protected function setUp()
    {
        $this->extension = new PaginationExtension();
        $this->container = new ContainerBuilder();

        $this->container->set('request_stack', new RequestStack());
        $this->container->set('router', new UrlGenerator(new RouteCollection(), new RequestContext()));

        $this->container->registerExtension($this->extension);
    }

    public function testContainer()
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
        $this->assertTrue($this->container->has('pagination.factory'));
        $this->assertTrue($this->container->has('pagination.data_source.array'));
        $this->assertTrue($this->container->has('pagination.data_source.query_set'));
        $this->assertTrue($this->container->has('pagination.handler'));
        $this->assertTrue($this->container->has('pagination.template_library'));
    }
}
