<?php

namespace Mindy\Template\Expression;

/**
 * Class AddExpression
 * @package Mindy\Template
 */
class AddExpression extends BinaryExpression
{
    public function operator()
    {
        return '+';
    }
}

