<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class ModExpression.
 */
class ModExpression extends BinaryExpression
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('fmod(', $indent);
        $this->left->compile($compiler);
        $compiler->raw(', ');
        $this->right->compile($compiler);
        $compiler->raw(')');
    }
}
