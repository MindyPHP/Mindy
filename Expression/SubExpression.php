<?php

namespace Mindy\Template\Expression;

/**
 * Class SubExpression.
 */
class SubExpression extends BinaryExpression
{
    public function operator()
    {
        return '-';
    }
}
