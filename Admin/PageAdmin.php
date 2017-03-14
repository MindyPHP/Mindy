<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Admin;

use Mindy\Bundle\AdminBundle\Admin\AbstractModelAdmin;
use Mindy\Bundle\PageBundle\Form\PageForm;
use Mindy\Bundle\PageBundle\Model\Page;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageAdmin.
 */
class PageAdmin extends AbstractModelAdmin
{
    public $treeLinkColumn = 'name';

    public $columns = ['name', 'url', 'is_published', 'published_at', 'is_index'];

    public $searchFields = ['name', 'id'];

    /**
     * @return Page
     */
    public function getModelClass()
    {
        return Page::class;
    }

    public function getActions()
    {
        return array_merge(parent::getActions(), [
            'publish' => 'Опубликовать',
            'unpublish' => 'Снять с публикации',
        ]);
    }

    public function actionUnpublish(Request $request)
    {
        $models = $request->get('models', []);
        if ($models) {
            Page::objects()->filter(['pk' => $_POST['models']])->update(['is_published' => false]);
        }

        return $this->redirect($this->getAdminUrl('list'));
    }

    public function actionPublish(Request $request)
    {
        $models = $request->get('models', []);
        if ($models) {
            Page::objects()->filter(['pk' => $_POST['models']])->update(['is_published' => true]);
        }

        return $this->redirect($this->getAdminUrl('list'));
    }

    public function getFormType()
    {
        return PageForm::class;
    }
}
