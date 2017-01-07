<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:57
 */

namespace Mindy\Bundle\AdminBundle\View;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFormView extends AbstractView
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @return FormInterface
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @param Request $request
     */
    public function handleRequest(Request $request)
    {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->handleForm($form);
            } else {

            }
        }
    }

    protected function handleForm(FormInterface $form)
    {

    }
}