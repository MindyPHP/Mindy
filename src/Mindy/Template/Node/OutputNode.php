<?php

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class OutputNode.
 */
class OutputNode extends Node
{
    /**
     * @var \Mindy\Template\NodeList
     */
    protected $expr;

    public function __construct($expr, $line)
    {
        parent::__construct($line);
        $this->expr = $expr;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('echo ', $indent);
        $this->expr->compile($compiler);
        $compiler->raw(";\n");
    }
}
