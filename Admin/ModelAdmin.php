<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 18:46
 */

namespace Mindy\Bundle\AdminBundle\Admin;

class ModelAdmin extends AbstractModelAdmin
{
    protected $formType;
    protected $modelClass;

    public function setFormType($formType)
    {
        $this->formType = $formType;
    }

    public function getFormType()
    {
        return $this->formType;
    }

    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }
}