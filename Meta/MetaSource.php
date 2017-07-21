<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SeoBundle\Meta;

/**
 * Class MetaSource
 */
class MetaSource
{
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
    protected $description;
    /**
     * @var string
     */
    protected $canonical;
    /**
     * @var array
     */
    protected $og = [];

    /**
     * @return array
     */
    public function getOg(): array
    {
        return $this->og;
    }

    /**
     * @param array $og
     */
    public function setOg(array $og)
    {
        $this->og = $og;
    }

    /**
     * @return string
     */
    public function getCanonical(): string
    {
        return $this->canonical;
    }

    /**
     * @param string $canonical
     */
    public function setCanonical(string $canonical)
    {
        $this->canonical = $canonical;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}
