<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
     *
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
            'form' => $this->getForm()->createView(),
        ];
    }
}
