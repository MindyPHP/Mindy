<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Interfaces;

interface IAdapter
{
    /**
     * @param $column
     *
     * @return string
     */
    public function quoteColumn($column);

    /**
     * @param $value
     *
     * @return string
     */
    public function quoteValue($value);

    /**
     * @param $tableName
     *
     * @return string
     */
    public function quoteTableName($tableName);
}
