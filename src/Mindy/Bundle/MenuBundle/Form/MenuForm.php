<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 11/12/2016
 * Time: 23:42.
 */

namespace Mindy\Bundle\MenuBundle\Form;

use Mindy\Bundle\MenuBundle\Model\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
