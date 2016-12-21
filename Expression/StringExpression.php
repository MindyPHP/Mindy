<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;
use Mindy\Template\Expression;

/**
 * Class StringExpression.
 */
class StringExpression extends Expression
{
    protected $value;

    public function __construct($value, $line)
    {
        parent::__construct($line);
        $this->value = $value;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->repr($this->value);
    }
}
