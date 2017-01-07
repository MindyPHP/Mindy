<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:45
 */

namespace Mindy\Bundle\AdminBundle\View;

use Mindy\Template\Renderer;

abstract class AbstractView implements ViewInterface
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * AbstractView constructor.
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate($view, array $parameters = array())
    {
        return $this->renderer->render($view, $parameters);
    }
}