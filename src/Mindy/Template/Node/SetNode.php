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
use Mindy\Template\NodeList;

/**
 * Class SetNode.
 */
class SetNode extends Node
{
    protected $name;
    protected $attrs;
    protected $value;

    public function __construct($name, $attrs, $value, $line)
    {
        parent::__construct($line);
        $this->name = $name;
        $this->attrs = $attrs;
        $this->value = $value;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $name = "\$context['$this->name']";
        if ($this->value instanceof NodeList) {
            $compiler->raw("ob_start();\n", $indent);
            $this->value->compile($compiler);
            $compiler->raw(
                "if (!isset($name)) $name = array();\n"."\n", $indent
            );
            $compiler->addTraceInfo($this, $indent);
            $compiler->raw("\$this->setAttr($name, array(", $indent);
            foreach ($this->attrs as $attr) {
                if (is_string($attr)) {
                    $compiler->repr($attr);
                } else {
                    $attr->compile($compiler);
                }
                $compiler->raw(', ');
            }
            $compiler->raw('), ob_get_clean());'."\n");
        } else {
            $compiler->raw(
                "if (!isset($name)) $name = array();\n"."\n", $indent
            );
            $compiler->addTraceInfo($this, $indent);
            $compiler->raw("\$this->setAttr($name, array(", $indent);
            foreach ($this->attrs as $attr) {
                is_string($attr) ?
                    $compiler->repr($attr) : $attr->compile($compiler);
                $compiler->raw(', ');
            }
            $compiler->raw('), ');
            $this->value->compile($compiler);
            $compiler->raw(");\n");
        }
    }
}
