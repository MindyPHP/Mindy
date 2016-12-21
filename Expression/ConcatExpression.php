<?php

namespace Mindy\Template\Expression;

/**
 * Class ConcatExpression.
 */
class ConcatExpression extends BinaryExpression
{
    public function operator()
    {
        return '.';
    }
}
