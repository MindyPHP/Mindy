<?php

namespace Mindy\Template\Expression;

/**
 * Class SubExpression
 * @package Mindy\Template
 */
class SubExpression extends BinaryExpression
{
    public function operator()
    {
        return '-';
    }
}

