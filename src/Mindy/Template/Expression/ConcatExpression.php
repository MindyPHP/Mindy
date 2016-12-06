<?php

namespace Mindy\Template\Expression;

/**
 * Class ConcatExpression
 * @package Mindy\Template
 */
class ConcatExpression extends BinaryExpression
{
    public function operator()
    {
        return '.';
    }
}

