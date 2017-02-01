<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
