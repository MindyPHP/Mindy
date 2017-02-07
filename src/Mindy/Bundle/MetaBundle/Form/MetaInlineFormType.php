<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\MetaBundle\Form;

use Mindy\Bundle\MetaBundle\Meta\MetaSourceInterface;
use Mindy\Bundle\MetaBundle\Model\Meta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MetaInlineFormType extends AbstractType
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

    protected function getHost()
    {
        return $this->request->getHost();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $options['source'];

        $builder
            ->add('host', TextType::class, [
                'label' => 'Хост',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'data' => $this->getHost(),
            ])
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

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($source) {
            $object = $event->getData();
            $form = $event->getForm();

            if ($source instanceof MetaSourceInterface) {
                $meta = $form->getData();
                $meta->setAttributes(array_merge($source->getMetaGenerator()->build(), [
                    'host' => $this->getHost()
                ]));
                dump($meta->save());die;
                $form->setData($meta);

                dump($form->getData());die;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
            'source' => false,
            'data_class' => Meta::class,
        ]);
    }
}
