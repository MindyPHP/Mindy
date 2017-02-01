<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
    protected function generateLoc($scheme, $host, $route, $parameters = [])
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
