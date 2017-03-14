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
use Mindy\Template\Node;

/**
 * Class MacroNode.
 */
class MacroNode extends Node
{
    protected $name;
    protected $args;
    protected $body;

    public function __construct($name, $args, $body, $line)
    {
        parent::__construct($line);
        $this->name = $name;
        $this->args = $args;
        $this->body = $body;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw("\n");
        $compiler->addTraceInfo($this, $indent, false);
        $compiler->raw(
            'public function macro_'.$this->name.
            '($params = array(), $context = array(), $macros = array(),'.
            ' $imports = array())'.
            "\n", $indent
        );
        $compiler->raw("{\n", $indent);

        $compiler->raw('$context = $params + array('."\n", $indent + 1);
        $i = 0;
        foreach ($this->args as $key => $val) {
            $compiler->raw(
                "'$key' => !isset(\$params['$key']) &&".
                " isset(\$params[$i]) ? \$params[$i] : ",
                $indent + 2
            );
            $val->compile($compiler);
            $compiler->raw(",\n");
            $i += 1;
        }
        $compiler->raw(") + \$context;\n", $indent + 1);

        $compiler->raw("ob_start();\n", $indent + 1);
        $this->body->compile($compiler, $indent + 1);
        $compiler->raw("return ob_get_clean();\n", $indent + 1);
        $compiler->raw("}\n", $indent);
    }
}
