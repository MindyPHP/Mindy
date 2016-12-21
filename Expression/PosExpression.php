<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class PosExpression.
 */
class PosExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('+');
    }
}
