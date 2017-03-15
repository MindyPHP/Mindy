<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
