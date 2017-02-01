<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin;

class TextHelper
{
    /**
     * @param $name
     *
     * @return string
     */
    public static function normalizeName($name)
    {
        return trim(strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name)), '_ ');
    }

    public static function shortName($className)
    {
        return (new \ReflectionClass($className))->getShortName();
    }
}
