<?php
/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType as BaseFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class FileType extends AbstractType
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * FileType constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mfile';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // this will be whatever class/entity is bound to your form (e.g. Media)
        $formInstance = $form->getParent()->getData();
        $assetName = isset($options['asset_name']) ? $options['asset_name'] : 'media';
        $fieldName = $form->getName();

        $fileUrl = null;
        if (null !== $formInstance) {
            $fileUrl = PropertyAccess::createPropertyAccessor()->getValue($formInstance, $fieldName);
            if (false === is_string($fileUrl)) {
                $fileUrl = '';
            }
        }

        // set an "image_url" variable that will be available when rendering this field
        $view->vars['asset_name'] = $assetName;
        $view->vars['file_url'] = $fileUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($builder) {
            $params = $this->requestStack->getMasterRequest()->request->all();
            $parentName = $event->getForm()->getParent()->getName();
<<<<<<< HEAD
            $formParams = isset($params[$parentName]) ? $params[$parentName] : $params;
=======
            $formParams = empty($parentName) ? $params : $params[$parentName];
>>>>>>> 6a11fbf09ab8c5b4d772ca8060458b5581ac722a

            if (
                isset($formParams[$builder->getName()]) &&
                $formParams[$builder->getName()] === '__remove'
            ) {
                $event->getForm()->setData('');
            } else if ($event->getData() === null) {
                $event->getForm()->getParent()->remove($builder->getName());
            }
        });
    }

    public function getParent()
    {
        return BaseFileType::class;
    }
}
