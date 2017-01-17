<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:57
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
     * @return $this
     */
    public function handleRequest(Request $request)
    {
        $this->getForm()->handleRequest($request);
        return $this;
    }
}