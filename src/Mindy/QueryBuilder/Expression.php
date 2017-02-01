<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\QueryBuilder;

class Expression
{
    private $expression = '';

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function toSQL()
    {
        return $this->expression;
    }
}
