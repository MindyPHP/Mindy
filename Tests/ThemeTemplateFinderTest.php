<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/01/2017
 * Time: 19:52
 */

namespace Mindy\Finder\Tests;

use Mindy\Finder\TemplateFinder;
use Mindy\Finder\ThemeTemplateFinder;

class ThemeTemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPaths()
    {
        $this->assertEquals([
            __DIR__ . '/fixtures/themes/default/templates'
        ], (new ThemeTemplateFinder(__DIR__ . '/fixtures', 'default'))->getPaths());

        // Custom template dir
        $this->assertEquals([
            __DIR__ . '/fixtures/themes/mobile/templates'
        ], (new ThemeTemplateFinder(__DIR__ . '/fixtures', 'mobile'))->getPaths());
    }

    public function testFind()
    {
        $this->assertEquals(
            null,
            (new ThemeTemplateFinder(__DIR__ . '/fixtures', 'default'))->find('base.html')
        );
        $this->assertEquals(
            __DIR__ . '/fixtures/themes/default/templates/core/file.html',
            (new ThemeTemplateFinder(__DIR__ . '/fixtures', 'default'))->find('core/file.html')
        );

        $this->assertEquals(
            __DIR__ . '/fixtures/themes/mobile/templates/base.html',
            (new ThemeTemplateFinder(__DIR__ . '/fixtures', 'mobile'))->find('base.html')
        );
        $this->assertEquals(
            null,
            (new ThemeTemplateFinder(__DIR__ . '/fixtures', 'mobile'))->find('core/file.html')
        );
    }
}
