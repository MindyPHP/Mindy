<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;
use Mindy\Template\Expression;

/**
 * Class ConditionalExpression.
 */
class ConditionalExpression extends Expression
{
    protected $expr1;
    protected $expr2;
    protected $expr3;

    public function __construct($expr1, $expr2, $expr3, $line)
    {
        parent::__construct($line);
        $this->expr1 = $expr1;
        $this->expr2 = $expr2;
        $this->expr3 = $expr3;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('((', $indent);
        $this->expr1->compile($compiler);
        $compiler->raw(') ? (');
        $this->expr2->compile($compiler);
        $compiler->raw(') : (');
        $this->expr3->compile($compiler);
        $compiler->raw('))');
    }
}
