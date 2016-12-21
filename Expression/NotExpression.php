<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class NotExpression.
 */
class NotExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('!');
    }
}
