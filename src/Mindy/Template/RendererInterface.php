<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Template;

interface RendererInterface
{
    /**
     * @param string $template absolute path to template
     * @param array  $data
     *
     * @return string
     */
    public function render($template, array $data = []);

    /**
     * @param string $template string template
     * @param array  $data
     *
     * @return string
     */
    public function renderString($template, array $data = []);
}
