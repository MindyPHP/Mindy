<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\MetaBundle\Meta;

/**
 * Class GenericGenerator
 */
class GenericGenerator extends AbstractGenerator
{
    /**
     * @var int
     */
    protected $titleLength = 60;
    /**
     * @var int
     */
    protected $keywordsLength = 60;
    /**
     * @var int
     */
    protected $descriptionLength = 160;
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
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            'description' => $this->buildDescription(),
            'keywords' => $this->buildKeywords(),
            'title' => $this->buildTitle(),
            'url' => $this->buildUrl(),
        ];
    }

    protected function buildUrl()
    {
        return $this->source->getAbsoluteUrl();
    }

    /**
     * @param $source
     *
     * @return string
     */
    protected function removeHtml($source)
    {
        return strip_tags($source);
    }

    /**
     * @return string
     */
    public function buildDescription()
    {
        $description = $this->propertyAccessor->getValue($this->source, $this->description);

        return mb_substr($this->removeHtml($description), 0, $this->descriptionLength, 'UTF-8');
    }

    /**
     * @return string
     */
    public function buildKeywords()
    {
        $keywords = $this->propertyAccessor->getValue($this->source, $this->keywords);

        // Remove all special characters to only leave alphanumeric characters (and whitespace)
        // Explode the phrase into an array, splitting by whitespace
        $keywordsArray = preg_split("/[\s,]+/", $this->removeHtml($keywords));

        // Create an empty array to store keywords
        $end = [];

        // Loop through each keyword
        foreach ($keywordsArray as $keyword) {
            // Check that the keyword is greater than 3 characters long
            // If it is, add it to the $end array
            if (strlen($keyword) > 3) {
                $end[] = strtolower($keyword);
            }
        }

        // Implode the $end array into a comma seperated list
        $result = implode(',', $end);
        while (mb_strlen($result) > $this->keywordsLength) {
            $temp = explode(',', $result);
            unset($temp[count($temp)]);
            $result = implode(',', $temp);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function buildTitle()
    {
        $title = $this->propertyAccessor->getValue($this->source, $this->title);

        return mb_substr($title, 0, $this->descriptionLength, 'UTF-8');
    }
}
