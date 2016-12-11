<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 10/10/2016
 * Time: 00:51
 */

namespace Mindy\Bundle\MetaBundle\Admin;

use Mindy\Bundle\MetaBundle\Form\MetaFilterForm;
use Mindy\Bundle\MetaBundle\Form\MetaFormType;
use Mindy\Bundle\MetaBundle\Model\Meta;
use Mindy\Bundle\MindyBundle\Admin\AbstractModelAdmin;

class MetaAdmin extends AbstractModelAdmin
{
    public $columns = ['domain', 'url', 'title'];

    public function getFilterFormType()
    {
        return MetaFilterForm::class;
    }

    /**
     * @return string model class name
     */
    public function getModelClass()
    {
        return Meta::class;
    }

    public function getFormType()
    {
        return MetaFormType::class;
    }
}