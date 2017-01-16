<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/01/2017
 * Time: 19:52
 */

namespace Mindy\Finder\Tests;

use Mindy\Finder\BundlesTemplateFinder;
use Mindy\Finder\ChainTemplateFinder;
use Mindy\Finder\TemplateFinder;

class ChainTemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $bundlesTemplateFinder = new BundlesTemplateFinder([
            __DIR__ . '/fixtures/bundles/Core',
            __DIR__ . '/fixtures/bundles/Page',
        ]);
        $templateFinder = new TemplateFinder(__DIR__ . '/fixtures');
        $this->finder = new ChainTemplateFinder([
            $templateFinder,
            $bundlesTemplateFinder
        ]);
    }

    public function testGetPaths()
    {
        $this->assertEquals([
            __DIR__ . '/fixtures/templates',
            __DIR__ . '/fixtures/bundles/Core/Resources/templates',
            __DIR__ . '/fixtures/bundles/Page/Resources/templates',
        ], $this->finder->getPaths());
    }

    public function testFind()
    {
        $this->assertEquals(
            __DIR__ . '/fixtures/bundles/Core/Resources/templates/core/settings.html',
            $this->finder->find('core/settings.html')
        );
        $this->assertEquals(
            __DIR__ . '/fixtures/templates/base.html',
            $this->finder->find('base.html')
        );
    }
}
