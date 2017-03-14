<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\MenuBundle\Form\Admin;

use Mindy\Bundle\AdminBundle\Form\Type\ButtonsType;
use Mindy\Bundle\MenuBundle\Model\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MenuForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $instance = $builder->getData();

        $builder
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => Menu::objects()->order(['root', 'lft'])->all(),
                'choice_label' => function ($menu) {
                    return sprintf('%s %s', str_repeat('-', $menu->level - 1), $menu);
                },
                'choice_value' => 'id',
                'choice_attr' => function ($menu) use ($instance) {
                    return $menu->pk == $instance->pk ? ['disabled' => 'disabled'] : [];
                },
            ])
            ->add('name', TextType::class, [
                'label' => 'Название',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Слаг',
                'help' => 'Ключ для выбора меню. Может содержать только латинские символы и цифры.',
            ])
            ->add('url', TextType::class, [
                'label' => 'Адрес',
                'help' => 'Ссылка может быть абсолютной, относительной или любым js кодом',
            ])
            ->add('buttons', ButtonsType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
