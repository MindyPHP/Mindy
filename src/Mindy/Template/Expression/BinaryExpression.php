<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

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
