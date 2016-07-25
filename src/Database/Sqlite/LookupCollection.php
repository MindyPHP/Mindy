<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:04
 */

namespace Mindy\QueryBuilder\Database\Sqlite;

use Mindy\QueryBuilder\BaseLookupCollection;
use Mindy\QueryBuilder\Interfaces\IAdapter;

class LookupCollection extends BaseLookupCollection
{
    public function has($lookup)
    {
        $lookups = [
            'regex', 'iregex', 'second', 'year', 'minute',
            'hour', 'day', 'month', 'week_day'
        ];
        if (in_array($lookup, $lookups)) {
            return true;
        } else {
            return parent::has($lookup);
        }
    }
    
    /**
     * @param IAdapter $adapter
     * @param $lookup
     * @param $column
     * @param $value
     * @return string
     */
    public function process(IAdapter $adapter, $lookup, $column, $value)
    {
        switch ($lookup) {
            case 'regex':
                return 'BINARY ' . $adapter->quoteColumn($column) . ' REGEXP /' . $value . '/';

            case 'iregex':
                return $adapter->quoteColumn($column) . ' REGEXP /' . $value . '/i';

            case 'second':
                return "strftime('%S', " . $adapter->quoteColumn($column) . ')=' . $value;

            case 'year':
                return "strftime('%Y', " . $adapter->quoteColumn($column) . ')=' . $value;

            case 'minute':
                return "strftime('%M', " . $adapter->quoteColumn($column) . ')=' . $value;

            case 'hour':
                return "strftime('%H', " . $adapter->quoteColumn($column) . ')=' . $value;

            case 'day':
                return "strftime('%d', " . $adapter->quoteColumn($column) . ')=' . $value;

            case 'month':
                return "strftime('%m', " . $adapter->quoteColumn($column) . ')=' . $value;

            case 'week_day':
                $value = (int)$value + 1;
                if (strlen($value) == 1) {
                    $value = "0" . (string)$value;
                }
                return "strftime('%w', " . $adapter->quoteColumn($column) . ')=' . $value;
        }

        return parent::process($adapter, $lookup, $column, $value);
    }
}