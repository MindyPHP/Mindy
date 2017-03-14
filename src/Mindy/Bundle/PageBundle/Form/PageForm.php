<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Mindy\Bundle\AdminBundle\Form\Type\ButtonsType;
use Mindy\Bundle\FormBundle\Form\DataTransformer\DateTimeTransformer;
use Mindy\Bundle\FormBundle\Form\Type\FileType;
use Mindy\Bundle\FormBundle\Form\Type\SlugType;
use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Bundle\PageBundle\TemplateLoader\PageTemplateLoaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PageForm extends AbstractType
{
    /**
     * @var
     */
    protected $templateLoader;

    /**
     * PageForm constructor.
     *
     * @param PageTemplateLoaderInterface $templateLoader
     */
    public function __construct(PageTemplateLoaderInterface $templateLoader)
    {
        $this->templateLoader = $templateLoader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $instance = $builder->getData();

        $builder
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => Page::objects()->order(['root', 'lft'])->all(),
                'choice_label' => function ($page) {
                    return sprintf('%s %s', str_repeat('-', $page->level - 1), $page);
                },
                'choice_value' => 'id',
                'choice_attr' => function ($page) use ($instance) {
                    return $page->pk == $instance->pk ? ['disabled' => 'disabled'] : [];
                },
            ])
            ->add('name', TextType::class, [
                'label' => 'Название',
            ])
            ->add('url', SlugType::class, [
                'label' => 'Слаг',
                'required' => false,
            ])
            ->add('content_short', TextareaType::class, [
                'label' => 'Краткое описание',
                'required' => false,
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Описание',
                'config_name' => 'default',
            ])
            ->add('published_at', DateTimeType::class, [
                'label' => 'Дата публикации',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('image', FileType::class, [
                'label' => 'Изображение',
                'required' => false,
                'constraints' => [
                    new Assert\Image([
                        'maxHeight' => 1280,
                        'maxWidth' => 1920,
                        'minHeight' => 100,
                        'minWidth' => 100,
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ]),
                ],
            ])
            ->add('is_index', CheckboxType::class, [
                'label' => 'Главная страница',
                'required' => false,
            ])
            ->add('is_published', CheckboxType::class, [
                'label' => 'Опубликовано',
                'required' => false,
            ])
            ->add('sorting', ChoiceType::class, [
                'label' => 'Сортировка дочерних страниц',
                'required' => false,
                'choices' => array_flip($instance->getSortingChoices()),
            ])
            ->add('view', ChoiceType::class, [
                'label' => 'Шаблон страницы',
                'required' => false,
                'choices' => $this->templateLoader->getTemplates(),
            ])
            ->add('view_children', ChoiceType::class, [
                'label' => 'Шаблон для дочерних страниц',
                'required' => false,
                'choices' => $this->templateLoader->getTemplates(),
                'preferred_choices' => function ($item, $key) use ($instance) {
                    return $instance->view_children == $item;
                },
            ])
            ->add('buttons', ButtonsType::class);

        $builder->get('published_at')->addModelTransformer(new DateTimeTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
