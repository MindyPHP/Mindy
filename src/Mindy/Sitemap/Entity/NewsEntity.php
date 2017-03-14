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
 * Class NewsEntity.
 */
class NewsEntity extends AbstractEntity
{
    /**
     * @var string
     */
    protected $publicationName;
    /**
     * @var string
     */
    protected $publicationLanguage;
    /**
     * @var string
     */
    protected $access;
    /**
     * @var string
     */
    protected $genres;
    /**
     * @var \DateTime
     */
    protected $publicationDate;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $keywords;
    /**
     * @var string
     */
    protected $stockTickers;

    public function __construct()
    {
        $this->publicationDate = new \DateTime();
    }

    /**
     * @return string
     */
    public function getPublicationName()
    {
        return $this->publicationName;
    }

    /**
     * @param string $publicationName
     *
     * @return $this
     */
    public function setPublicationName($publicationName)
    {
        $this->publicationName = $publicationName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicationLanguage()
    {
        return $this->publicationLanguage;
    }

    /**
     * @param string $publicationLanguage
     *
     * @return $this
     */
    public function setPublicationLanguage($publicationLanguage)
    {
        $this->publicationLanguage = $publicationLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string $access
     *
     * @return $this
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return string
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param string $genres
     *
     * @return $this
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTime $publicationDate
     *
     * @return $this
     */
    public function setPublicationDate(\DateTime $publicationDate)
    {
        $this->publicationDate = $publicationDate;

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
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return string
     */
    public function getStockTickers()
    {
        return $this->stockTickers;
    }

    /**
     * @param string $stockTickers
     *
     * @return $this
     */
    public function setStockTickers($stockTickers)
    {
        $this->stockTickers = $stockTickers;

        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $newsText = '<news:news>';
        $newsText .= '<news:publication>';
        $newsText .= '<news:name>'.$this->publicationName.'</news:name>';
        $newsText .= '<news:language>'.$this->publicationLanguage.'</news:language>';
        $newsText .= '</news:publication>';
        if (!empty($this->access)) {
            $newsText .= '<news:access>'.$this->access.'</news:access>';
        }
        if (!empty($this->genres)) {
            $newsText .= '<news:genres>'.$this->genres.'</news:genres>';
        }
        if (!empty($this->publicationDate)) {
            $newsText .= '<news:publication_date>'.$this->publicationDate->format('c').'</news:publication_date>';
        }
        if (!empty($this->title)) {
            $newsText .= '<news:title>'.$this->title.'</news:title>';
        }
        if (!empty($this->keywords)) {
            $newsText .= '<news:keywords>'.$this->keywords.'</news:keywords>';
        }
        if (!empty($this->stockTickers)) {
            $newsText .= '<news:stock_tickers>'.$this->stockTickers.'</news:stock_tickers>';
        }
        $newsText .= '</news:news>';

        return $newsText;
    }
}
