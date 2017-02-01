<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template;

interface RendererInterface
{
    /**
     * @param string $template absolute path to template
     * @param array $data
     *
     * @return string
     */
    public function render($template, array $data = []);

    /**
     * @param string $template string template
     * @param array $data
     *
     * @return string
     */
    public function renderString($template, array $data = []);
}
