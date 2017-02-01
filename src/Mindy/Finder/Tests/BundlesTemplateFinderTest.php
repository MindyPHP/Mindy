<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Finder\Tests;

use Mindy\Finder\BundlesTemplateFinder;
use Mindy\Finder\TemplateFinder;

class BundlesTemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPaths()
    {
        $this->assertEquals([
            __DIR__.'/fixtures/templates',
        ], (new TemplateFinder(__DIR__.'/fixtures'))->getPaths());
    }

    public function testFind()
    {
        $finder = new BundlesTemplateFinder([
            __DIR__.'/fixtures/bundles/Core',
            __DIR__.'/fixtures/bundles/Page',
        ]);

        $this->assertEquals(
            __DIR__.'/fixtures/bundles/Core/Resources/templates/core/settings.html',
            $finder->find('core/settings.html')
        );
        $this->assertEquals(
            __DIR__.'/fixtures/bundles/Page/Resources/templates/page/view.html',
            $finder->find('page/view.html')
        );

        $this->assertEquals(
            null,
            $finder->find('core/missing.html')
        );
    }
}
