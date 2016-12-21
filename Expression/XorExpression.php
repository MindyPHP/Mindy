<?php

namespace Mindy\Template\Expression;

/**
 * Class XorExpression.
 */
class XorExpression extends BinaryExpression
{
    public function operator()
    {
        return 'xor';
    }
}
