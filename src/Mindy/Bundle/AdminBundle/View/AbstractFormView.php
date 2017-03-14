<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\View;

use Mindy\Orm\ModelInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFormView extends AbstractView
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
     * @var ModelInterface
     */
    protected $model;

    /**
     * @param $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        if ($this->formInstance === null) {
            $this->formInstance = $this
                ->container
                ->get('form.factory')
                ->create($this->form, $this->model, [
                    'method' => 'POST',
                    'attr' => ['enctype' => 'multipart/form-data'],
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
}
