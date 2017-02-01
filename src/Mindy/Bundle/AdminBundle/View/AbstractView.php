<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\View;

use Mindy\Template\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractView implements ViewInterface
{
    use ContainerAwareTrait;

    /**
     * @var RendererInterface
     */
    protected $renderer;
    /**
     * @var string
     */
    protected $template;

    /**
     * AbstractView constructor.
     *
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(array $parameters = [])
    {
        return $this->renderer->render($this->template, array_merge($this->getContextData(), $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $parameters = [], $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->renderTemplate($parameters));

        return $response;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param Request $request
     */
    abstract public function handleRequest(Request $request);
}
