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
use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Node;

/**
 * Class ExtendsNode.
 */
class ExtendsNode extends Node
{
    protected $parent;
    protected $params;

    public function __construct($parent, $params, $line)
    {
        parent::__construct($line);
        $this->parent = $parent;
        $this->params = $params;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('$this->parent = $this->loadExtends(', $indent);
        $this->parent->compile($compiler);
        $compiler->raw(');'."\n");

        $compiler->raw('if (isset($this->parent)) {'."\n", $indent);
        if ($this->params instanceof ArrayExpression) {
            $compiler->raw('$context = ', $indent + 1);
            $this->params->compile($compiler);
            $compiler->raw(' + $context;'."\n");
        }
        $compiler->raw(
            'return $this->parent->display'.
            '($context, $blocks + $this->blocks, $macros + $this->macros,'.
            ' $imports + $this->imports);'.
            "\n", $indent + 1
        );
        $compiler->raw("}\n", $indent);
    }
}
