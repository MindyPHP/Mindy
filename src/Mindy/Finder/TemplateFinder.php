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
 * Class TemplateFinder
 */
class TemplateFinder implements TemplateFinderInterface
{
    /**
     * @var string
     */
    protected $basePath;
    /**
     * @var string
     */
    protected $templatesDir;

    /**
     * TemplateFinder constructor.
     *
     * @param $basePath
     * @param string $templatesDir
     */
    public function __construct($basePath, $templatesDir = 'templates')
    {
        $this->basePath = $basePath;
        $this->templatesDir = $templatesDir;
    }

    /**
     * {@inheritdoc}
     */
    public function find($templatePath)
    {
        $path = implode(DIRECTORY_SEPARATOR, [$this->basePath, $this->templatesDir, $templatePath]);
        if (is_file($path)) {
            return $path;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        return [
            implode(DIRECTORY_SEPARATOR, [$this->basePath, $this->templatesDir]),
        ];
    }
}
