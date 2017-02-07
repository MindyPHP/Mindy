<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Mindy\Bundle\AdminBundle\Form\Type\ButtonsType;
use Mindy\Bundle\FormBundle\Form\DataTransformer\DateTimeTransformer;
use Mindy\Bundle\MetaBundle\Form\MetaFormType;
use Mindy\Bundle\MetaBundle\Form\MetaInlineFormType;
use Mindy\Bundle\MetaBundle\Meta\MetaSourceInterface;
use Mindy\Bundle\MetaBundle\Model\Meta;
use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Bundle\PageBundle\TemplateLoader\PageTemplateLoaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('url', TextType::class, [
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
            ->add('meta', MetaInlineFormType::class, [
                'data' => new Meta,
                'source' => $instance
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
