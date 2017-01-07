<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 22:22
 */

namespace Mindy\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteConfirmForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirm', CheckboxType::class, [
                'label' => 'Подтверждаю удаление'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Подтвердить удаление',
                'attr' => ['class' => 'b-button']
            ]);
    }
}