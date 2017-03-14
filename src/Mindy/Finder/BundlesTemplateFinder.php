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
 * Class BundlesTemplateFinder
 */
class BundlesTemplateFinder implements TemplateFinderInterface
{
    /**
     * @var array
     */
    protected $bundlesDirs = [];
    /**
     * @var string
     */
    protected $templatesDir;

    /**
     * BundlesTemplateFinder constructor.
     *
     * @param array  $bundlesDirs
     * @param string $templatesDir
     */
    public function __construct(array $bundlesDirs, $templatesDir = 'templates')
    {
        $this->bundlesDirs = $bundlesDirs;
        $this->templatesDir = $templatesDir;
    }

    /**
     * {@inheritdoc}
     */
    public function find($templatePath)
    {
        foreach ($this->bundlesDirs as $dir) {
            $path = implode(DIRECTORY_SEPARATOR, [$dir, 'Resources', $this->templatesDir, $templatePath]);

            if (is_file($path)) {
                return $path;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        $paths = [];
        foreach ($this->bundlesDirs as $dir) {
            if ($extra = glob($dir.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.$this->templatesDir)) {
                $paths = array_merge($paths, $extra);
            }
        }

        return $paths;
    }
}
