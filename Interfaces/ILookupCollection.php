<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\QueryBuilder\Interfaces;

interface ILookupCollection
{
    /**
     * @param $lookup
     *
     * @return bool
     */
    public function has($lookup);

    /**
     * @param $lookup
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function process(IAdapter $adapter, $lookup, $column, $value);
}
