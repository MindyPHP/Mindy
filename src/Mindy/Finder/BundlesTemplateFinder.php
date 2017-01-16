<?php
/**
 * User: max
 * Date: 05/10/2016
 * Time: 21:19.
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

        return;
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
