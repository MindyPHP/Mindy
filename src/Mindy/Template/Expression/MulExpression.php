<?php

namespace Mindy\Template\Expression;

/**
 * Class MulExpression
 * @package Mindy\Template
 */
class MulExpression extends BinaryExpression
{
    public function operator()
    {
        return '*';
    }
}
