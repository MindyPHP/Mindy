<?php

namespace Mindy\Bundle\SitemapBundle\Sitemap\Entity;

use Mindy\Bundle\SitemapBundle\Sitemap\Collection\SiteMapCollection;

/**
 * Class SiteMapIndexEntity
 * @package Mindy\Bundle\SitemapBundle\Entity
 * https://support.google.com/webmasters/answer/75712
 */
class SiteMapIndexEntity extends AbstractEntity
{
    /**
     * @var SiteMapCollection
     */
    protected $siteMapCollection;

    /**
     *
     */
    public function __construct()
    {
        $this->siteMapCollection = new SiteMapCollection();
    }

    /**
     * @param SiteMapEntity $siteMapEntity
     * @return $this
     */
    public function addSiteMap(SiteMapEntity $siteMapEntity)
    {
        $this->siteMapCollection->attach($siteMapEntity);

        return $this;
    }

    /**
     * @return SiteMapCollection
     */
    public function getSiteMapCollection()
    {
        return $this->siteMapCollection;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $siteMapIndexText = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $siteMapIndexText .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($this->siteMapCollection as $siteMapEntity) {
            $siteMapIndexText .= '<sitemap>';
            $siteMapIndexText .= '<loc>' . $siteMapEntity->getLoc() . '</loc>';
            $siteMapIndexText .= '<lastmod>' . $siteMapEntity->getLastmod()->format('Y-m-d') . '</lastmod>';
            $siteMapIndexText .= '</sitemap>';
        }
        $siteMapIndexText .= '</sitemapindex>';

        return $siteMapIndexText;
    }
}