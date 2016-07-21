<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 12:46
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\Mysql\Adapter;
use Mindy\QueryBuilder\QueryBuilder;

class CustomLookupTest extends \PHPUnit_Framework_TestCase
{
    public function testCustom()
    {
        $lookups = [
            'foo' => function (IAdapter $adapter, $column, $value) {
                return $adapter->quoteColumn($column) . ' ??? ' . $adapter->quoteValue($value);
            }
        ];
        $qb = new QueryBuilder(new Adapter(null, $lookups), new Legacy($lookups));
        $qb->select('*')->from('test')->where(['name__foo' => 1]);
        $this->assertEquals($qb->toSQL(), 'SELECT * FROM `test` WHERE `name` ??? 1');
    }
}