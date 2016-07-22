<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:04
 */

namespace Mindy\QueryBuilder\Database\Mysql;

use Mindy\QueryBuilder\BaseLookupCollection;
use Mindy\QueryBuilder\Interfaces\IAdapter;

class LookupCollection extends BaseLookupCollection
{
    /**
     * @return array
     */
    public function getLookups()
    {
        return array_merge(parent::getLookups(), [
            'regex' => function (IAdapter $adapter, $column, $value) {
                return 'BINARY ' . $adapter->quoteColumn($column) . ' REGEXP ' . $value;
            },
            'iregex' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' REGEXP ' . $value;
            },
            'second' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(SECOND FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'year' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(YEAR FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'minute' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(MINUTE FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'hour' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(HOUR FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'day' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(DAY FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'month' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(MONTH FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'week_day' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(DAYOFWEEK FROM ' . $adapter->quoteColumn($column) . ')=' . $value;
            },
        ]);
    }
}