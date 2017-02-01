<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MenuBundle\Library;

use Mindy\Bundle\MenuBundle\Model\Menu;
use Mindy\Template\Library;
use Mindy\Template\Renderer;

class MenuLibrary extends Library
{
    protected $template;

    /**
     * MenuLibrary constructor.
     *
     * @param Renderer $template
     */
    public function __construct(Renderer $template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'get_menu' => function ($slug, $template = 'menu/menu.html') {
                $menu = Menu::objects()->get(['slug' => $slug]);
                if ($menu === null) {
                    return '';
                }

                return $this->template->render($template, [
                    'items' => $menu->objects()->descendants()->asTree()->all(),
                ]);
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
