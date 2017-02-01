<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Finder\Tests;

use Mindy\Finder\TemplateFinder;

class TemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPaths()
    {
        $this->assertEquals([
            __DIR__.'/fixtures/templates',
        ], (new TemplateFinder(__DIR__.'/fixtures'))->getPaths());

        // Custom template dir
        $this->assertEquals([
            __DIR__.'/fixtures/foobar',
        ], (new TemplateFinder(__DIR__.'/fixtures', 'foobar'))->getPaths());
    }

    public function testFind()
    {
        $this->assertEquals(
            __DIR__.'/fixtures/templates/base.html',
            (new TemplateFinder(__DIR__.'/fixtures'))->find('base.html')
        );
        $this->assertEquals(
            __DIR__.'/fixtures/templates/core/file.html',
            (new TemplateFinder(__DIR__.'/fixtures'))->find('core/file.html')
        );
    }
}
