<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\CKEditorBundle\Library;

use Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Mindy\Template\Library;

class CKEditorLibrary extends Library
{
    /**
     * @var \Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface
     */
    private $renderer;

    /**
     * Creates a CKEditor Twig extension.
     *
     * @param \Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface $renderer the CKEditor renderer
     */
    public function __construct(CKEditorRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function renderBasePath($basePath)
    {
        return $this->renderer->renderBasePath($basePath);
    }

    /**
     * {@inheritdoc}
     */
    public function renderJsPath($jsPath)
    {
        return $this->renderer->renderJsPath($jsPath);
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget($id, array $config, array $options = [])
    {
        return $this->renderer->renderWidget($id, $config, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function renderDestroy($id)
    {
        return $this->renderer->renderDestroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function renderPlugin($name, array $plugin)
    {
        return $this->renderer->renderPlugin($name, $plugin);
    }

    /**
     * {@inheritdoc}
     */
    public function renderStylesSet($name, array $stylesSet)
    {
        return $this->renderer->renderStylesSet($name, $stylesSet);
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate($name, array $template)
    {
        return $this->renderer->renderTemplate($name, $template);
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'ckeditor_base_path' => function ($basePath) {
                return $this->renderBasePath($basePath);
            },
            'ckeditor_js_path' => function ($jsPath) {
                return $this->renderJsPath($jsPath);
            },
            'ckeditor_widget' => function ($id, array $config, array $options = []) {
                return $this->renderWidget($id, $config, $options);
            },
            'ckeditor_destroy' => function ($id) {
                return $this->renderDestroy($id);
            },
            'ckeditor_plugin' => function ($name, array $plugin) {
                return $this->renderPlugin($name, $plugin);
            },
            'ckeditor_styles_set' => function ($name, array $stylesSet) {
                return $this->renderStylesSet($name, $stylesSet);
            },
            'ckeditor_template' => function ($name, array $template) {
                return $this->renderTemplate($name, $template);
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
