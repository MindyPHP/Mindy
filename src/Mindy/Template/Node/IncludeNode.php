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
use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Node;

/**
 * Class IncludeNode.
 */
class IncludeNode extends Node
{
    protected $include;
    protected $params;

    public function __construct($include, $params, $line)
    {
        parent::__construct($line);
        $this->include = $include;
        $this->params = $params;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('$this->loadInclude(', $indent);
        $this->include->compile($compiler);
        $compiler->raw(')->display(');

        if ($this->params instanceof ArrayExpression) {
            $this->params->compile($compiler);
            $compiler->raw(' + ');
        }

        $compiler->raw('$context);'."\n");
    }
}
