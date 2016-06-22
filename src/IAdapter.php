<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:27
 */

namespace Mindy\QueryBuilder;

interface IAdapter
{
    public function quoteColumn($column);

    public function quoteValue($value);

    public function quoteTableName($tableName);
}