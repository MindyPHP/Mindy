<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 00:15.
 */

namespace Mindy\Bundle\MetaBundle\Admin;

use Mindy\Bundle\MetaBundle\Form\TemplateForm;
use Mindy\Bundle\MetaBundle\Model\Template;
use Mindy\Bundle\MindyBundle\Admin\AbstractModelAdmin;

class TemplateAdmin extends AbstractModelAdmin
{
    public $columns = ['code'];

    public function getFormType()
    {
        return TemplateForm::class;
    }

    public function getModelClass()
    {
        return Template::class;
    }
}
