<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:53
 */

namespace Mindy\Bundle\AdminBundle\View;

use Symfony\Component\Form\Test\FormInterface;

class DeleteView extends AbstractFormView
{
    public function handleForm(FormInterface $form)
    {
        $instance = $form->getData();
        if ($instance->delete()) {

        }
    }
}