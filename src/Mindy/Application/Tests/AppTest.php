<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Application\Tests;

use Mindy\Application\App;
use Symfony\Component\Filesystem\Filesystem;

class AppTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        App::shutdown();

        $fs = new Filesystem();
        $fs->remove(__DIR__.'/runtime');
    }

    public function testInit()
    {
        $app = App::createInstance(AppKernel::class, 'dev', true);
        $this->assertInstanceOf(App::class, $app);

        $this->assertTrue(method_exists($app, 'getUser'));
        $this->assertTrue(method_exists($app, 'hasComponent'));
        $this->assertTrue(method_exists($app, 'getComponent'));
    }
}
