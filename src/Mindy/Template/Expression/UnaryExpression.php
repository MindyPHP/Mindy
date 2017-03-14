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
 * Class UnaryExpression.
 */
class UnaryExpression extends Expression
{
    protected $node;

    public function __construct($node, $line)
    {
        parent::__construct($line);
        $this->node = $node;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('(', $indent);
        $this->operator($compiler);
        $compiler->raw('(');
        $this->node->compile($compiler);
        $compiler->raw('))');
    }
}
