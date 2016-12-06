<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class NotExpression
 * @package Mindy\Template
 */
class NotExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('!');
    }
}

