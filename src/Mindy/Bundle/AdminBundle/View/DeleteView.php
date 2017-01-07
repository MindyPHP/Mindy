<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:53
 */

namespace Mindy\Bundle\AdminBundle\View;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DeleteView extends AbstractView
{
    /**
     * @var FormInterface
     */
    protected $form;
    /**
     * @var FormInterface
     */
    protected $formInstance;

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        if ($this->formInstance === null) {
            $this->formInstance = $this
                ->container
                ->get('form.factory')
                ->create($this->form, [], [
                    'method' => 'POST',
                ]);
        }
        return $this->formInstance;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->getForm()->isSubmitted() && $this->getForm()->isValid();
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function handleRequest(Request $request)
    {
        $this->getForm()->handleRequest($request);
        return $this;
    }

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