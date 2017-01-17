<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 20:04.
 */

namespace Mindy\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractSitemapProvider.
 */
abstract class AbstractSitemapProvider implements SitemapProviderInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * PageProvider constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return $this
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }

    /**
     * @param $scheme
     * @param $host
     * @param $route
     * @param array $parameters
     *
     * @return string
     */
    protected function generateLoc($scheme, $host, $route, $parameters = array())
    {
        if (null === $this->urlGenerator) {
            throw new \RuntimeException('UrlGenerator interface is missing');
        }
        $this->urlGenerator->getContext()->setHost($host);
        $this->urlGenerator->getContext()->setScheme($scheme);

        return $this
            ->urlGenerator
            ->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
