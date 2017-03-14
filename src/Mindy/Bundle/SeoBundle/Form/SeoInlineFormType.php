<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\SeoBundle\Form;

use Mindy\Bundle\SeoBundle\Meta\MetaSourceInterface;
use Mindy\Bundle\SeoBundle\Model\Seo;
use Mindy\Orm\ModelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SeoInlineFormType extends AbstractType
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * MetaInlineFormType constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return string
     */
    protected function getHost()
    {
        return $this->request->getHost();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('canonical', TextType::class, [
                'label' => 'Абсолютный адрес (canonical)',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'Заголовок (title)',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 60]),
                ],
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Ключевые слова (keywords)',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 60]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание (description)',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 160]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
            'data_class' => Seo::class,
        ]);
    }
}
