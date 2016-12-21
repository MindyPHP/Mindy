<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28/11/2016
 * Time: 23:43.
 */

namespace Mindy\Bundle\MetaBundle\Form;

use Mindy\Bundle\MetaBundle\Model\Meta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MetaFormType extends AbstractType
{
    protected $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('host', TextType::class, [
                'label' => 'Хост',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'data' => $this->request->getHost(),
            ])
            ->add('url', TextType::class, [
                'label' => 'Адрес страницы',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('canonical', TextType::class, [
                'label' => 'Абсолютный адрес (canonical)',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'Заголовок (title)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 60]),
                ],
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Ключевые слова (keywords)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 60]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание (description)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 160]),
                ],
            ]);

        if (!$options['inline']) {
            $builder
                ->add('submit', SubmitType::class, [
                    'label' => 'Сохранить',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'inline' => false,
            'data_class' => Meta::class,
        ]);
    }
}
