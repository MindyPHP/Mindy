<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/16
 * Time: 11:07
 */

namespace Mindy\Bundle\SitemapBundle\Sitemap;

interface SitemapProviderInterface
{
    /**
     * @return array
     */
    public function build();
}