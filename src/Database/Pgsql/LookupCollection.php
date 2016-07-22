<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 11:08
 */

namespace Mindy\QueryBuilder\Database\Pgsql;

use Mindy\QueryBuilder\BaseLookupCollection;
use Mindy\QueryBuilder\Interfaces\IAdapter;

class LookupCollection extends BaseLookupCollection
{
    public function getLookups()
    {
        return array_merge(parent::getLookups(), [
            'second' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(SECOND FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
            'year' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(YEAR FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
            'minute' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(MINUTE FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
            'hour' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(HOUR FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
            'day' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(DAY FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
            'month' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(MONTH FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
            'week_day' => function (IAdapter $adapter, $column, $value) {
                return 'EXTRACT(DOW FROM ' . $adapter->quoteColumn($column) . '::timestamp)=' . $value;
            },
        ]);
    }
}