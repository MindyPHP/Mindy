<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:38
 */

namespace Mindy\QueryBuilder;

abstract class BaseLookupCollection implements ILookupCollection
{
    /**
     * @return array
     */
    public function getLookups()
    {
        return [
            'exact' => function (IAdapter $adapter, $column, $value) {
                /** @var $adapter \Mindy\QueryBuilder\BaseAdapter */
                return $adapter->quoteColumn($column) . '=' . $adapter->quoteValue($value);
            },
            'gte' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . '>=' . $adapter->quoteValue($value);
            },
            'gt' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . '>' . $adapter->quoteValue($value);
            },
            'lte' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . '<=' . $adapter->quoteValue($value);
            },
            'lt' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . '<' . $adapter->quoteValue($value);
            },
            'range' => function (IAdapter $adapter, $column, $value) {
                list($min, $max) = $value;
                return $adapter->quoteColumn($column) . ' BETWEEN ' . $adapter->quoteValue($min) . ' AND ' . $adapter->quoteValue($max);
            },
            'isnull' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' ' . ((bool)$value ? 'IS NULL' : 'IS NOT NULL');
            },
            'contains' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' LIKE %' . $adapter->quoteValue($value) . '%';
            },
            'icontains' => function (IAdapter $adapter, $column, $value) {
                return 'LOWER(' . $adapter->quoteColumn($column) . ') LIKE %' . $adapter->quoteValue(mb_strtolower($value, 'UTF-8')) . '%';
            },
            'startswith' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' LIKE ' . $adapter->quoteValue($value) . '%';
            },
            'istartswith' => function (IAdapter $adapter, $column, $value) {
                return 'LOWER(' . $adapter->quoteColumn($column) . ') LIKE ' . $adapter->quoteValue(mb_strtolower($value, 'UTF-8')) . '%';
            },
            'endswith' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' LIKE %' . $adapter->quoteValue($value);
            },
            'iendswith' => function (IAdapter $adapter, $column, $value) {
                return 'LOWER(' . $adapter->quoteColumn($column) . ') LIKE %' . $adapter->quoteValue(mb_strtolower($value, 'UTF-8'));
            },
            'in' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' IN (' . implode(',', $value) . ')';
            },
            'raw' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' ' . $value;
            },
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
        ];
    }

    /**
     * @param $lookup
     * @return bool
     */
    public function has($lookup)
    {
        return array_key_exists($lookup, $this->getLookups());
    }

    /**
     * @param $lookup
     * @param $column
     * @param $value
     * @return mixed
     */
    public function run($adapter, $lookup, $column, $value)
    {
        $lookups = $this->getLookups();
        return $lookups[$lookup]->__invoke($adapter, $column, $value);
    }
}