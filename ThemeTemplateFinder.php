<?php
/**
 * User: max
 * Date: 05/10/2016
 * Time: 21:19.
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

        return;
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
