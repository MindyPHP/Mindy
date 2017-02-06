<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template;

class DefaultLibrary extends Library
{
    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'is_array' => 'is_array',
            'is_object' => 'is_object',
            'is_string' => 'is_string',
            'number_format' => 'number_format',
            'nl2br' => 'nl2br',
            'substr_count' => 'substr_count',
            'dirname' => 'dirname',
            'basename' => 'basename',
            'time' => 'time',
            'strtotime' => 'strtotime',
            'strtr' => 'strtr',
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
