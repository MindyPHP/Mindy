<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Finder;

/**
 * Class ThemeTemplateFinder
 */
class ThemeTemplateFinder implements TemplateFinderInterface
{
    /**
     * @var string
     */
    protected $basePath;
    /**
     * @var string
     */
    protected $theme;
    /**
     * @var string
     */
    protected $templatesDir;

    /**
     * ThemeTemplateFinder constructor.
     *
     * @param $basePath
     * @param $theme
     * @param string $templatesDir
     */
    public function __construct($basePath, $theme, $templatesDir = 'templates')
    {
        $this->basePath = $basePath;
        $this->theme = $theme;
        $this->templatesDir = $templatesDir;
    }

    /**
     * @param $templatePath
     *
     * @return null|string absolute path of template if founded
     */
    public function find($templatePath)
    {
        $path = implode(DIRECTORY_SEPARATOR, [$this->basePath, 'themes', $this->theme, $this->templatesDir, $templatePath]);
        if (is_file($path)) {
            return $path;
        }
    }

    /**
     * @return array of available template paths
     */
    public function getPaths()
    {
        return [
            implode(DIRECTORY_SEPARATOR, [$this->basePath, 'themes', $this->theme, $this->templatesDir]),
        ];
    }
}
