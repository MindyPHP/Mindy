<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:27.
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
