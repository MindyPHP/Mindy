<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class AuthForm extends AbstractType
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * AuthForm constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_target_path', HiddenType::class, [
                'data' => $this->router->generate('admin_index'),
            ])
            ->add('_username', TextType::class, [
                'label' => 'Номер телефона',
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Пароль',
            ])
            ->add('_remember', CheckboxType::class, [
                'label' => 'Запомнить',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Вход',
            ]);
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
