<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 13:06
 */

namespace Mindy\QueryBuilder\Mysql;

use Mindy\QueryBuilder\BaseAdapter;
use Mindy\QueryBuilder\IAdapter;

class Adapter extends BaseAdapter implements IAdapter
{
    /**
     * Quotes a table name for use in a query.
     * A simple table name has no schema prefix.
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, "`") !== false ? $name : "`" . $name . "`";
    }

    /**
     * Quotes a column name for use in a query.
     * A simple column name has no prefix.
     * @param string $name column name
     * @return string the properly quoted column name
     */
    public function quoteSimpleColumnName($name)
    {
        return strpos($name, '`') !== false || $name === '*' ? $name : '`' . $name . '`';
    }

    /**
     * @return array
     */
    public function getLookupCollection()
    {
        return new LookupCollection($this);
    }

    public function getRandomOrder()
    {
        return 'RANDOM()';
    }

    public function convertToDateTime($value = null)
    {
        static $dateTimeFormat = "Y-m-d H:i:s";
        if ($value === null) {
            $value = date($dateTimeFormat);
        } elseif (is_numeric($value)) {
            $value = date($dateTimeFormat, $value);
        } elseif (is_string($value)) {
            $value = date($dateTimeFormat, strtotime($value));
        }
        return $value;
    }

    public function convertToBoolean($value)
    {
        return (bool)$value ? 1 : 0;
    }
}