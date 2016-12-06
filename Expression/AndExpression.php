<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class AndExpression
 * @package Mindy\Template
 */
class AndExpression extends LogicalExpression
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('(!($a = ', $indent);
        $this->left->compile($compiler);
        $compiler->raw(') ? ($a) : (');
        $this->right->compile($compiler);
        $compiler->raw('))');
    }
}

