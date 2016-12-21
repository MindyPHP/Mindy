<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 00:16.
 */

namespace Mindy\Bundle\MetaBundle\Form;

use Mindy\Bundle\MetaBundle\Model\Template;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Код шаблона',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Шаблон',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Template::class,
        ]);
    }
}
