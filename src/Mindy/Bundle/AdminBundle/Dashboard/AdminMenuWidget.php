<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Dashboard;

use Mindy\Bundle\AdminBundle\Admin\AdminMenu;
use Mindy\Bundle\DashboardBundle\Dashboard\AbstractWidget;

class AdminMenuWidget extends AbstractWidget
{
    protected $adminMenu;

    public function __construct(AdminMenu $adminMenu)
    {
        $this->adminMenu = $adminMenu;
    }

    public function getTemplate()
    {
        return 'admin/dashboard/menu.html';
    }

    public function getData()
    {
        return [
            'adminMenu' => $this->adminMenu->getMenu(),
        ];
    }
}
