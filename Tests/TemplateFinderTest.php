<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/01/2017
 * Time: 19:52
 */

namespace Mindy\Finder\Tests;

use Mindy\Finder\TemplateFinder;

class TemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPaths()
    {
        $this->assertEquals([
            __DIR__ . '/fixtures/templates'
        ], (new TemplateFinder(__DIR__ . '/fixtures'))->getPaths());

        // Custom template dir
        $this->assertEquals([
            __DIR__ . '/fixtures/foobar'
        ], (new TemplateFinder(__DIR__ . '/fixtures', 'foobar'))->getPaths());
    }

    public function testFind()
    {
        $this->assertEquals(
            __DIR__ . '/fixtures/templates/base.html',
            (new TemplateFinder(__DIR__ . '/fixtures'))->find('base.html')
        );
        $this->assertEquals(
            __DIR__ . '/fixtures/templates/core/file.html',
            (new TemplateFinder(__DIR__ . '/fixtures'))->find('core/file.html')
        );
    }
}
