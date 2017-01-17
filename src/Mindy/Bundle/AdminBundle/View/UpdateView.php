<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:53
 */

namespace Mindy\Bundle\AdminBundle\View;

class UpdateView extends AbstractFormView
{
    /**
     * @return array
     */
    public function getContextData()
    {
        return [
            'instance' => $this->model,
            'form' => $this->getForm()->createView()
        ];
    }
}