<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/02/2017
 * Time: 21:37
 */

namespace Mindy\Bundle\SeoBundle\Helper;

class SeoHelper
{
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
     * @param $source
     * @param int $length
     *
     * @return string
     */
    public function generateDescription($source, $length = 160)
    {
        return mb_substr($this->removeHtml($source), 0, $length, 'UTF-8');
    }

    /**
     * @param $source
     * @param int $length
     * @param int $minLength
     *
     * @return string
     */
    public function generateKeywords($source, $length = 60, $minLength = 3)
    {
        // Remove all special characters to only leave alphanumeric characters (and whitespace)
        // Explode the phrase into an array, splitting by whitespace
        $keywords = preg_split("/[\s,]+/", $this->removeHtml($source));

        // Create an empty array to store keywords
        $end = [];

        // Loop through each keyword
        foreach ($keywords as $keyword) {
            // Check that the keyword is greater than 3 characters long
            // If it is, add it to the $end array
            if (strlen($keyword) > $minLength) {
                $end[] = mb_strtolower($keyword, 'UTF-8');
            }
        }

        // Implode the $end array into a comma seperated list
        $result = implode(',', $end);
        while (mb_strlen($result) > $length) {
            $temp = explode(',', $result);
            unset($temp[count($temp)]);
            $result = implode(',', $temp);
        }

        return $result;
    }

    /**
     * @param $source
     * @param int $length
     *
     * @return string
     */
    public function generateTitle($source, $length = 60)
    {
        return mb_substr($source, 0, $length, 'UTF-8');
    }
}