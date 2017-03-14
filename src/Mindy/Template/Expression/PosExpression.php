<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class PosExpression.
 */
class PosExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('+');
    }
}
