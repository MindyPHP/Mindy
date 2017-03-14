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
 * Class AttributeExpression.
 */
class AttributeExpression extends Expression
{
    protected $node;
    protected $attr;
    protected $args;

    public function __construct($node, $attr, $args, $line)
    {
        parent::__construct($line);
        $this->node = $node;
        $this->attr = $attr;
        $this->args = $args;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('$this->getAttr(', $indent);
        $this->node->compile($compiler);
        $compiler->raw(', ');
        $this->attr->compile($compiler);
        if (is_array($this->args)) {
            $compiler->raw(', array(');
            foreach ($this->args as $arg) {
                $arg->compile($compiler);
                $compiler->raw(', ');
            }
            $compiler->raw(')');
        } else {
            $compiler->raw(', false');
        }
        $compiler->raw(')');
    }
}
