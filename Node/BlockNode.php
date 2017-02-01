<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class BlockNode.
 */
class BlockNode extends Node
{
    protected $name;
    protected $body;

    public function __construct($name, $body, $line)
    {
        parent::__construct($line);
        $this->name = $name;
        $this->body = $body;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw("\n");
        $compiler->addTraceInfo($this, $indent, false);
        $compiler->raw(
            'public function block_'.$this->name.
            '($context, $blocks = array(), $macros = array(),'.
            ' $imports = array())'."\n", $indent
        );
        $compiler->raw("{\n", $indent);
        $this->body->compile($compiler, $indent + 1);
        $compiler->raw("}\n", $indent);
    }
}
