<?php

namespace Mindy\Sitemap\Entity;

use Mindy\Sitemap\Collection\LocationCollection;

/**
 * Class SiteMapEntity.
 */
class SiteMapEntity extends AbstractEntity
{
    /**
     * @var LocationCollection
     */
    protected $locationCollection;
    /**
     * @var string
     */
    protected $loc;
    /**
     * @var \DateTime
     */
    protected $lastmod;

    /**
     *
     */
    public function __construct()
    {
        $this->lastmod = new \DateTime();
        $this->locationCollection = new LocationCollection();
    }

    /**
     * @param LocationEntity $locationEntity
     *
     * @return $this
     */
    public function addLocation(LocationEntity $locationEntity)
    {
        $this->locationCollection->attach($locationEntity);

        return $this;
    }

    /**
     * @return LocationCollection
     */
    public function getLocationCollection()
    {
        return $this->locationCollection;
    }

    /**
     * @return string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    public function setLoc($location)
    {
        $this->loc = $location;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return $this
     */
    public function setLastmod(\DateTime $dateTime)
    {
        $this->lastmod = $dateTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $siteMapText = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $siteMapText .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
        foreach ($this->locationCollection as $locationEntity) {
            $siteMapText .= $locationEntity->getXml();
        }
        $siteMapText .= '</urlset>';

        return $siteMapText;
    }
}
