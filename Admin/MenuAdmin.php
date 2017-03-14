<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\MenuBundle\Admin;

use Mindy\Bundle\AdminBundle\Admin\AbstractModelAdmin;
use Mindy\Bundle\MenuBundle\Form\MenuForm;
use Mindy\Bundle\MenuBundle\Model\Menu;
use Mindy\Orm\ModelInterface;
use Symfony\Component\HttpFoundation\Request;

class MenuAdmin extends AbstractModelAdmin
{
    public $columns = ['name', 'slug', 'url'];

    public $searchFields = ['name'];

    /**
     * @var string
     */
    public $treeLinkColumn = 'name';

    public function getCustomBreadrumbs(Request $request, ModelInterface $model, $action)
    {
        $breadcrumbs = [];
        if ($model->getIsNewRecord()) {
            if ($parentId = $request->query->get('parent_id')) {
                $model = Menu::objects()->get(['id' => $parentId]);
                if ($model === null) {
                    return [];
                }
            } else {
                return [];
            }
        }

        $parents = $model->objects()->ancestors(true)->order(['lft'])->all();
        foreach ($parents as $ancestor) {
            $breadcrumbs[] = [
                'name' => (string) $ancestor,
                'url' => $this->getAdminUrl('list').'?'.http_build_query(['pk' => $ancestor->id]),
            ];
        }

        return $breadcrumbs;
    }

    public function getModelClass()
    {
        return Menu::class;
    }

    public function getFormType()
    {
        return MenuForm::class;
    }
}
