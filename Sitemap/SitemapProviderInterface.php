<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/16
 * Time: 11:07
 */

namespace Mindy\Bundle\SitemapBundle\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Interface SitemapProviderInterface
 * @package Mindy\Bundle\SitemapBundle\Sitemap
 */
interface SitemapProviderInterface
{
    /**
     * @param string $scheme
     * @param string $host
     * @return \Generator
     */
    public function build($scheme, $host);

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @return $this
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator);
}