<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class NegExpression.
 */
class NegExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('-');
    }
}
