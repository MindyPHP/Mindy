<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
