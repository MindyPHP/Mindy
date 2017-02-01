<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template;

/**
 * Class NodeList.
 */
class NodeList extends Node
{
    /**
     * @var Node[]
     */
    protected $nodes;

    public function __construct($nodes, $line)
    {
        parent::__construct($line);
        $this->nodes = $nodes;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        foreach ($this->nodes as $node) {
            $node->compile($compiler, $indent);
        }
    }
}
