<?php

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class TextNode.
 */
class TextNode extends Node
{
    protected $data;

    public function __construct($data, $line)
    {
        parent::__construct($line);
        $this->data = $data;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        if (!strlen($this->data)) {
            return;
        }
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('echo ', $indent);
        $compiler->repr($this->data);
        $compiler->raw(";\n");
    }
}
