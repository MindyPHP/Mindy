<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\DashboardBundle\Dashboard;

use Mindy\Template\Renderer;

class Dashboard
{
    /**
     * @var array
     */
    protected $widgets = [];

    /**
     * @var Renderer
     */
    protected $template;

    /**
     * Dashboard constructor.
     *
     * @param Renderer $template
     */
    public function __construct(Renderer $template)
    {
        $this->template = $template;
    }

    /**
     * @param WidgetInterface $widget
     */
    public function addWidget(WidgetInterface $widget)
    {
        $this->widgets[] = $widget;
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array_map(function ($widget) {
            return $this->template->render($widget->getTemplate(), $widget->getData());
        }, $this->widgets);
    }
}
