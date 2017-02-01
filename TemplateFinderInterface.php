<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Finder;

/**
 * Interface TemplateFinderInterface.
 */
interface TemplateFinderInterface
{
    /**
     * @param $templatePath
     *
     * @return null|string absolute path of template if founded
     */
    public function find($templatePath);

    /**
     * @return array of available template paths
     */
    public function getPaths();
}
