<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin;

/**
 * Class AdminMenu.
 */
class AdminMenu
{
    /**
     * @var array
     */
    protected $menu = [];

    /**
     * AdminMenu constructor.
     *
     * @param array $menu
     */
    public function __construct(array $menu = [])
    {
        $this->menu = $menu;
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        // todo add check permissions
        return $this->menu;
    }
}
