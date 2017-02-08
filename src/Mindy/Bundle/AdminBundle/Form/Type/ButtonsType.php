<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Form\Type;

use Mindy\Bundle\MindyBundle\Traits\AbsoluteUrlInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
            ])
            ->add('save_and_return', SubmitType::class, [
                'label' => 'Сохранить и вернуться',
            ])
            ->add('save_and_create', SubmitType::class, [
                'label' => 'Сохранить и создать',
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $instance = $form->getParent()->getData();
        if ($instance->getIsNewRecord() === false && $instance instanceof AbsoluteUrlInterface) {
            $view->vars['link'] = $instance->getAbsoluteUrl();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
        ]);
    }
}
