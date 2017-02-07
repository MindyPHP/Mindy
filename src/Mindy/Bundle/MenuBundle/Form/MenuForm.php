<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MenuBundle\Form;

use Mindy\Bundle\AdminBundle\Form\Type\ButtonsType;
use Mindy\Bundle\MenuBundle\Model\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    return sprintf("%s %s", str_repeat('-', $menu->level - 1), $menu);
                },
                'choice_value' => 'id',
                'choice_attr' => function($menu) use ($instance) {
                    return $menu->pk == $instance->pk ? ['disabled' => 'disabled'] : [];
                },
            ])
            ->add('name', TextType::class, [
                'label' => 'Название'
            ])
            ->add('slug', TextType::class, [
                'label' => 'Слаг',
                'required' => false,
                'help' => 'Ключ для выбора меню. Может содержать только латинские символы и цифры.'
            ])
            ->add('url', TextType::class, [
                'label' => 'Адрес',
                'required' => false,
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
