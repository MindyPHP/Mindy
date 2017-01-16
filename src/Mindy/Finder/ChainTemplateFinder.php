<?php
/**
 * User: max
 * Date: 05/10/2016
 * Time: 21:21.
 */

namespace Mindy\Finder;

/**
 * Class ChainTemplateFinder.
 */
class ChainTemplateFinder implements TemplateFinderInterface
{
    /**
     * @var TemplateFinderInterface[]
     */
    protected $finders = [];

    /**
     * Finder constructor.
     *
     * @param TemplateFinderInterface[] $finders
     */
    public function __construct(array $finders = [])
    {
        $this->finders = (array)$finders;
    }

    /**
     * @param TemplateFinderInterface $finder
     */
    public function addFinder(TemplateFinderInterface $finder)
    {
        $this->finders[] = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function find($templatePath)
    {
        $templates = [];
        foreach ($this->finders as $finder) {
            $template = $finder->find($templatePath);
            if ($template !== null) {
                $templates[] = $template;
            }
        }

        return array_shift($templates);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        $paths = [];
        foreach ($this->finders as $finder) {
            $paths = array_merge($paths, $finder->getPaths());
        }

        return $paths;
    }
}
