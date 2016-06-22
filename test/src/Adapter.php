<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:31
 */

use Mindy\QueryBuilder\BaseAdapter;
use Mindy\QueryBuilder\IAdapter;

/**
 * Dummy adapter is a mysql adapter without quoting
 * Class DummyAdapter
 * @package Mindy\QueryBuilder\Adapter
 */
class Adapter extends BaseAdapter implements IAdapter
{
    public function quoteColumn($column)
    {
        return $column;
    }

    public function quoteValue($value)
    {
        return $value;
    }

    public function quoteTableName($value)
    {
        return $value;
    }

    /**
     * @return array
     */
    public function getLookupCollection()
    {
        return new LookupCollection;
    }

    public function getRandomOrder()
    {
        return '?';
    }

    public function convertToDateTime($value = null)
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    public function convertToBoolean($value)
    {
        return 'RANDOM()';
    }
}