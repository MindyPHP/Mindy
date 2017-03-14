<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            'is_numeric' => 'is_numeric',
            'get_class' => 'get_class',
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
