<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:45
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
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(array $parameters = array())
    {
        return $this->renderer->render($this->template, array_merge($this->getContextData(), $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $parameters = array(), $response = null)
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