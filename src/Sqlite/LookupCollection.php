<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:04
 */

namespace Mindy\QueryBuilder\Sqlite;

use Mindy\QueryBuilder\BaseLookupCollection;
use Mindy\QueryBuilder\IAdapter;

class LookupCollection extends BaseLookupCollection
{
    /**
     * @return array
     */
    public function getLookups()
    {
        return array_merge(parent::getLookups(), [
            'regex' => function (IAdapter $adapter, $column, $value) {
                return 'BINARY ' . $adapter->quoteColumn($column) . ' REGEXP /' . $value . '/';
            },
            'iregex' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' REGEXP /' . $value . '/i';
            },
            'second' => function (IAdapter $adapter, $column, $value) {
                return "strftime('%S', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'year' => function (IAdapter $adapter, $column, $value) {
                return "strftime('%Y', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'minute' => function (IAdapter $adapter, $column, $value) {
                return "strftime('%M', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'hour' => function (IAdapter $adapter, $column, $value) {
                return "strftime('%H', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'day' => function (IAdapter $adapter, $column, $value) {
                return "strftime('%d', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'month' => function (IAdapter $adapter, $column, $value) {
                return "strftime('%m', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
            'week_day' => function (IAdapter $adapter, $column, $value) {
                $value = (int)$value + 1;
                if (strlen($value) == 1) {
                    $value = "0" . (string)$value;
                }
                return "strftime('%w', " . $adapter->quoteColumn($column) . ')=' . $value;
            },
        ]);
    }
}