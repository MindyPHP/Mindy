<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MindyBundle\Form\DataTransformer;

use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;
use Symfony\Component\Form\DataTransformerInterface;

class QuerySetTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value instanceof Manager || $value instanceof QuerySet) {
            return $value->all();
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return $value;
    }
}
