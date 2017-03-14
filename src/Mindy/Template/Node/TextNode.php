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
