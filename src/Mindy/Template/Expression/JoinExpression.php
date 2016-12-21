<?php

namespace Mindy\Template\Expression;

/**
 * Class JoinExpression.
 */
class JoinExpression extends BinaryExpression
{
    public function operator()
    {
        return ".' '.";
    }
}
