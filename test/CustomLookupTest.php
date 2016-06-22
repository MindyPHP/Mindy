<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 12:46
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\IAdapter;
use Mindy\QueryBuilder\LegacyLookupBuilder;
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
        $qb = new QueryBuilder(new Adapter(null, $lookups), new LegacyLookupBuilder());
        $qb->setTypeSelect()->setSelect('*')->setFrom('test')->setWhere([
            'name__foo' => 1
        ]);
        $this->assertEquals($qb->toSQL(), 'SELECT * FROM `test` WHERE `name` ??? 1');
    }
}