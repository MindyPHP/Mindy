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
 * Interface SitemapProviderInterface.
 */
interface SitemapProviderInterface
{
    /**
     * @param string $scheme
     * @param string $host
     *
     * @return \Generator
     */
    public function build($scheme, $host);

    /**
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return $this
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator);
}
