<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:00
 */

namespace Mindy\QueryBuilder\Tests;

use Adapter;
use Mindy\QueryBuilder\LookupBuilder;
use Mindy\QueryBuilder\LookupBuilder\Simple;
use Mindy\QueryBuilder\QueryBuilder;
use Mindy\QueryBuilder\Sqlite\LookupCollection;

class LookupBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function lookupProvider()
    {
        return [
            1 => [['id' => 1], '[[id]]=1'],
            2 => [['id' => ['exact' => 1]], '[[id]]=1'],
            3 => [['id' => ['gte' => 1]], '[[id]]>=1'],
            4 => [['id' => ['lte' => 1]], '[[id]]<=1'],
            5 => [['id' => ['gt' => 1]], '[[id]]>1'],
            6 => [['id' => ['lt' => 1]], '[[id]]<1'],
            7 => [['id' => ['range' => [1, 2]]], '[[id]] BETWEEN 1 AND 2'],
            8 => [['id' => ['isnull' => true]], '[[id]] IS NULL'],
            9 => [['id' => ['isnull' => false]], '[[id]] IS NOT NULL'],
            10 => [['id' => ['contains' => 'FOO']], '[[id]] LIKE @%FOO%@'],
            11 => [['id' => ['icontains' => 'FOO']], 'LOWER([[id]]) LIKE @%foo%@'],
            12 => [['id' => ['startswith' => 'FOO']], '[[id]] LIKE @FOO%@'],
            13 => [['id' => ['istartswith' => 'FOO']], 'LOWER([[id]]) LIKE @foo%@'],
            14 => [['id' => ['endswith' => 'FOO']], '[[id]] LIKE @%FOO@'],
            15 => [['id' => ['iendswith' => 'FOO']], 'LOWER([[id]]) LIKE @%foo@'],
            16 => [['id' => ['in' => [1, 2, 'test']]], '[[id]] IN (1, 2, @test@)'],
            17 => [['id' => ['raw' => "?? [[qwe]]"]], "[[id]] ?? [[qwe]]"],
        ];
    }

    /**
     * @dataProvider lookupProvider
     */
    public function testLookups($where, $whereSql)
    {
        $collection = new LookupCollection();
        $builder = new Simple($collection->getLookups());
        list($lookup, $column, $value) = current($builder->parse($where));
        $adapter = new \Mindy\QueryBuilder\Mysql\Adapter;
        $this->assertEquals(str_replace('@', "'", $adapter->quoteSql($whereSql)), $collection->run($adapter, $lookup, $column, $value));
    }
}