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
 * Class ContinueNode.
 */
class ContinueNode extends Node
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw("continue;\n", $indent);
    }
}
