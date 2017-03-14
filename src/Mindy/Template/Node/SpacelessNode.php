<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;

/**
 * Class SpacelessNode.
 */
class SpacelessNode extends OutputNode
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('ob_start();ob_implicit_flush(false);', $indent);
        $this->expr->compile($compiler);
        $compiler->raw(";\n");
        $compiler->raw("echo trim(preg_replace('/>\\s+</', '><', ob_get_clean()));\n", $indent);
    }
}
