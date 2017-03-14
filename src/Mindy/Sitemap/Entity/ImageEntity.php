<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Sitemap\Entity;

/**
 * Class ImageEntity.
 */
class ImageEntity extends AbstractEntity
{
    /**
     * @var string
     */
    protected $loc;
    /**
     * @var string
     */
    protected $caption;
    /**
     * @var string
     */
    protected $geoLocation;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $license;

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->loc;
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        $this->loc = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     *
     * @return $this
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @return string
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param string $geoLocation
     *                            Example: place of photo (country, city...)
     *
     * @return $this
     */
    public function setGeoLocation($geoLocation)
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @param string $license
     *
     * @return $this
     */
    public function setLicense($license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $imageText = '<image:image>';
        $imageText .= '<image:loc>'.$this->loc.'</image:loc>';
        if (!empty($this->caption)) {
            $imageText .= '<image:caption>'.$this->caption.'</image:caption>';
        }
        if (!empty($this->geoLocation)) {
            $imageText .= '<image:geo_location>'.$this->geoLocation.'</image:geo_location>';
        }
        if (!empty($this->title)) {
            $imageText .= '<image:title>'.$this->title.'</image:title>';
        }
        if (!empty($this->license)) {
            $imageText .= '<image:license>'.$this->license.'</image:license>';
        }
        $imageText .= '</image:image>';

        return $imageText;
    }
}
