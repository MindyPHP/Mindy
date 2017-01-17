<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 18:27
 */

namespace Mindy\Template;

interface RendererInterface
{
    /**
     * @param string $template absolute path to template
     * @param array $data
     * @return string
     */
    public function render($template, array $data = []);

    /**
     * @param string $template string template
     * @param array $data
     * @return string
     */
    public function renderString($template, array $data = []);
}