<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Template\Expression;

/**
 * Class DivExpression.
 */
class DivExpression extends BinaryExpression
{
    public function operator()
    {
        return '/';
    }
}
