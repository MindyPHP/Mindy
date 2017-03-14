<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\TemplateLoader;

use Mindy\Finder\TemplateFinderInterface;
use Symfony\Component\Finder\Finder;

class PageTemplateLoader implements PageTemplateLoaderInterface
{
    /**
     * @var TemplateFinderInterface
     */
    protected $templateFinder;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * PageTemplateLoader constructor.
     *
     * @param string                  $basePath
     * @param TemplateFinderInterface $templateFinder
     */
    public function __construct($basePath, TemplateFinderInterface $templateFinder)
    {
        $this->basePath = $basePath;
        $this->templateFinder = $templateFinder;
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected function formatPath($path)
    {
        return sprintf('%s/page/templates', $path);
    }

    /**
     * @return array
     */
    protected function fetchCorrectPaths()
    {
        $paths = $this->templateFinder->getPaths();

        return array_filter($paths, function ($path) {
            return is_dir($this->formatPath($path));
        });
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        $templates = [];
        foreach ($this->fetchCorrectPaths() as $path) {
            $targetPath = $this->formatPath($path);

            $finder = (new Finder())
                ->ignoreUnreadableDirs()
                ->files()
                ->in($targetPath)
                ->name('*.html');

            foreach ($finder as $template) {
                /* @var $template \SplFileInfo */
                $path = $template->getRealPath();

                $templates[substr($path, strlen($this->basePath) + 1)] = $template->getBasename();
            }
        }

        return $templates;
    }
}
