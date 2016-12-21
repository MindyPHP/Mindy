<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;
use Mindy\Template\Expression;

/**
 * Class BinaryExpression.
 */
class BinaryExpression extends Expression
{
    protected $left;
    protected $right;

    public function __construct($left, $right, $line)
    {
        parent::__construct($line);
        $this->left = $left;
        $this->right = $right;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $op = $this->operator($compiler);
        $compiler->raw('(', $indent);
        $this->left->compile($compiler);
        $compiler->raw(' '.$op.' ');
        $this->right->compile($compiler);
        $compiler->raw(')');
    }
}
