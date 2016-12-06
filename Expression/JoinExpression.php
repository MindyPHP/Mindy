<?php

namespace Mindy\Template\Expression;

/**
 * Class JoinExpression
 * @package Mindy\Template
 */
class JoinExpression extends BinaryExpression
{
    public function operator()
    {
        return ".' '.";
    }
}

