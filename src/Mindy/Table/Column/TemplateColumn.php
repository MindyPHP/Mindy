<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28/11/16
 * Time: 09:56.
 */

namespace Mindy\Component\Table\Column;

use Mindy\Template\Renderer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class TemplateColumn extends AbstractColumn
{
    protected $renderer;

    protected $template;

    public function __construct(RequestStack $requestStack, RouterInterface $router, Renderer $renderer)
    {
        parent::__construct($requestStack, $router);

        $this->renderer = $renderer;
    }

    public function getValue($row)
    {
        return $this->renderer->render($this->template, ['row' => $row]);
    }
}
