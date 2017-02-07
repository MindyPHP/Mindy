<?php

namespace Mindy\Bundle\PageBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Mindy\Bundle\AdminBundle\Form\Type\ButtonsType;
use Mindy\Bundle\PageBundle\Model\Page;
use Mindy\Finder\ChainTemplateFinder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class PageForm extends AbstractType
{
    protected $chainTemplateFinder;
    protected $translator;

    public function __construct(ChainTemplateFinder $chainTemplateFinder, TranslatorInterface $translator)
    {
        $this->chainTemplateFinder = $chainTemplateFinder;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $instance = $builder->getData();

        $builder
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => Page::objects()->order(['root', 'lft'])->all(),
                'choice_label' => function ($page) {
                    return sprintf("%s %s", str_repeat('-', $page->level - 1), $menu);
                },
                'choice_value' => 'id',
                'choice_attr' => function($page) use ($instance) {
                    return $page->pk == $instance->pk ? ['disabled' => 'disabled'] : [];
                },
            ])
            ->add('name', TextType::class, [
                'label' => 'Название',
            ])
            ->add('url', TextType::class, [
                'label' => 'Слаг',
            ])
            ->add('content_short', TextareaType::class, [
                'label' => 'Краткое описание',
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Описание',
                'config_name' => 'default',
            ])
            ->add('published_at', TextType::class, [
                'label' => 'Дата публикации',
            ])
            ->add('is_index', CheckboxType::class, [
                'label' => 'Главная страница',
            ])
            ->add('is_published', CheckboxType::class, [
                'label' => 'Опубликовано',
            ])
            ->add('sorting', ChoiceType::class, [
                'label' => 'Сортировка дочерних страниц',
                'required' => false,
                'choices' => array_flip($instance->getSortingChoices()),
            ])
            ->add('view', ChoiceType::class, [
                'label' => 'Шаблон страницы',
                'required' => false,
                'choices' => $this->getTemplates(),
            ])
            ->add('view_children', ChoiceType::class, [
                'label' => 'Шаблон для дочерних страниц',
                'required' => false,
                'choices' => $this->getTemplates(),
                'preferred_choices' => function ($item, $key) use ($instance) {
                    return $instance->view_children == $item;
                },
            ])
            ->add('buttons', ButtonsType::class);
    }

    public function getTemplates()
    {
        $templates = [null => ''];

        foreach ($this->chainTemplateFinder->getPaths() as $path) {
            $targetPath = sprintf('%s/page/templates', $path);

            if (false == is_dir($targetPath)) {
                continue;
            }

            $finder = (new Finder())
                ->ignoreUnreadableDirs()
                ->files()
                ->in($targetPath)
                ->name('*.html');

            foreach ($finder as $template) {
                if (strpos($targetPath, 'Bundle') !== false) {
                    $optGroup = $this->translator->trans('page.page.bundle');
                } elseif (strpos($targetPath, 'themes') !== false) {
                    $optGroup = $this->translator->trans('page.page.theme');
                } else {
                    $optGroup = $this->translator->trans('page.page.app');
                }

                $path = $template->getRealPath();
                $relativePath = substr($path, strpos($path, 'templates') + 10);

                $templates[$optGroup][$relativePath] = $template->getBasename();
            };
        }

        return $templates;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
