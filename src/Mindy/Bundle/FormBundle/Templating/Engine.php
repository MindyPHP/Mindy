<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FormBundle\Templating;

use Mindy\Finder\TemplateFinderInterface;
use Mindy\Template\RendererInterface;

class Engine implements EngineInterface
{
    protected $renderer;
    protected $templateFinder;

    /**
     * Engine constructor.
     *
     * @param RendererInterface $renderer
     * @param TemplateFinderInterface $templateFinder
     */
    public function __construct(RendererInterface $renderer, TemplateFinderInterface $templateFinder)
    {
        $this->renderer = $renderer;
        $this->templateFinder = $templateFinder;
    }

    /**
     * @return string The evaluated template as a string
     */
    public function render($name, array $parameters = [])
    {
        return $this->renderer->render($name, $parameters);
    }

    /**
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name)
    {
        return null !== $this->templateFinder->find($name);
    }
}
