<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:52
 */

namespace Mindy\Bundle\AdminBundle\View;

class CreateView extends AbstractFormView
{
    /**
     * @return array
     */
    public function getContextData()
    {
        return [
            'form' => $this->getForm()->createView()
        ];
    }
}