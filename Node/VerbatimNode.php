<?php

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;

/**
 * TODO
 * Class VerbatimNode.
 */
class VerbatimNode extends OutputNode
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
    }
}
