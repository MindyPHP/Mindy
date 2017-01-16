<?php
/**
 * User: max
 * Date: 05/10/2016
 * Time: 21:19.
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

        return;
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
