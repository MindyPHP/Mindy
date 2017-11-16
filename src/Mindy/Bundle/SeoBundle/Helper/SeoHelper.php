<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        $keywords = preg_split('/[\\s,]+/', $this->removeHtml($source));

        // Create an empty array to store keywords
        $end = [];

        // Loop through each keyword
        foreach ($keywords as $keyword) {
            // Check that the keyword is greater than 3 characters long
            // If it is, add it to the $end array
            if (mb_strlen($keyword, 'UTF-8') <= $minLength) {
                continue;
            }

            $end[] = mb_strtolower($keyword, 'UTF-8');
        }

        while (mb_strlen(implode(',', $end), 'UTF-8') > $length) {
            $end = array_slice($end, 0, count($end) - 1);
        }
        return implode(',', $end);
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
