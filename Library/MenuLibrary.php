<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\MenuBundle\Library;

use Mindy\Bundle\MenuBundle\Model\Menu;
use Mindy\Template\Library;
use Mindy\Template\RendererInterface;

class MenuLibrary extends Library
{
    /**
     * @var RendererInterface
     */
    protected $template;

    /**
     * MenuLibrary constructor.
     *
     * @param RendererInterface $template
     */
    public function __construct(RendererInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'render_menu' => function ($slug, $template = 'menu/menu.html') {
                return $this->renderMenu($slug, $template);
            },
            'get_menu' => function ($slug) {
                return $this->getMenu($slug);
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

    /**
     * @param string $slug
     * @return null
     */
    protected function getMenu($slug)
    {
        $menu = Menu::objects()->get(['slug' => $slug]);
        if ($menu === null) {
            return [];
        }

        return $menu
            ->objects()
            ->descendants()
            ->asTree()
            ->all();
    }

    /**
     * @param string $slug
     * @param string $template
     *
     * @return null|string
     */
    protected function renderMenu($slug, $template = 'menu/menu.html')
    {
        $items = $this->getMenu($slug);

        if (empty($items)) {
            return null;
        }

        return $this->template->render($template, [
            'items' => $items,
        ]);
    }
}
