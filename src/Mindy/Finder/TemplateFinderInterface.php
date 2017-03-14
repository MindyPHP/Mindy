<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
