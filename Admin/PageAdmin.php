<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 06/10/16
 * Time: 11:29.
 */

namespace Mindy\Bundle\PageBundle\Admin;

use Mindy\Bundle\MindyBundle\Admin\AbstractModelAdmin;
use Mindy\Bundle\PageBundle\Form\PageFormType;
use Mindy\Bundle\PageBundle\Model\Page;
use function Mindy\trans;

/**
 * Class PageAdmin.
 */
class PageAdmin extends AbstractModelAdmin
{
    public $treeLinkColumn = 'name';

    public $columns = ['name', 'is_published', 'is_index'];

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
            'publish' => trans('page.admin.publish'),
            'unpublish' => trans('page.admin.unpublish'),
        ]);
    }

    public function actionUnpublish()
    {
        $models = $this->getRequest()->request->get('models', []);
        if ($models) {
            Page::objects()->filter(['pk' => $_POST['models']])->update(['is_published' => false]);
        }

        return $this->redirect($this->getAdminUrl('list'));
    }

    public function actionPublish()
    {
        $models = $this->getRequest()->request->get('models', []);
        if ($models) {
            Page::objects()->filter(['pk' => $_POST['models']])->update(['is_published' => true]);
        }

        return $this->redirect($this->getAdminUrl('list'));
    }

    public function getFormType()
    {
        return PageFormType::class;
    }
}
