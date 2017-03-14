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
 * Class InclusionExpression.
 */
class InclusionExpression extends LogicalExpression
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        // if (is_array($haystack))

        $compiler->raw('(is_array(', $indent);
        $this->right->compile($compiler);
        $compiler->raw(') ? ');

        // {

        $compiler->raw('(in_array(', $indent);
        $this->left->compile($compiler);
        $compiler->raw(', (array)');
        $this->right->compile($compiler);
        $compiler->raw('))');

        // } else

        $compiler->raw(' : ', $indent);

        // {

        $compiler->raw('(mb_strstr(', $indent);
        $this->right->compile($compiler);
        $compiler->raw(', ');
        $this->left->compile($compiler);
        $compiler->raw(') != false)');

        // }

        $compiler->raw(')');
    }
}
