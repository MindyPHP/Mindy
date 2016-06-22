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
use Mindy\QueryBuilder\QueryBuilder;

class LookupBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function lookupProvider()
    {
        return [
            [['id' => 1], 'id=1'],
            [['id' => ['exact' => 1]], 'id=1'],
            [['id' => ['gte' => 1]], 'id>=1'],
            [['id' => ['lte' => 1]], 'id<=1'],
            [['id' => ['gt' => 1]], 'id>1'],
            [['id' => ['lt' => 1]], 'id<1'],
            [['id' => ['range' => [1, 2]]], 'id BETWEEN 1 AND 2'],
            [['id' => ['isnull' => true]], 'id IS NULL'],
            [['id' => ['isnull' => false]], 'id IS NOT NULL'],
            [['id' => ['contains' => 'FOO']], 'id LIKE %FOO%'],
            [['id' => ['icontains' => 'FOO']], 'LOWER(id) LIKE %foo%'],
            [['id' => ['startswith' => 'FOO']], 'id LIKE FOO%'],
            [['id' => ['istartswith' => 'FOO']], 'LOWER(id) LIKE foo%'],
            [['id' => ['in' => [1, 2]]], 'id IN (1,2)'],
            [['id' => ['raw' => "REGEXP '^[abc]'"]], "id REGEXP '^[abc]'"],
            [['id' => ['regex' => '^[abc]']], "BINARY id REGEXP ^[abc]"],
            [['id' => ['iregex' => '^[abc]']], "id REGEXP ^[abc]"],
            [['id' => ['second' => '10']], "EXTRACT(SECOND FROM id)=10"],
            [['id' => ['day' => '10']], "EXTRACT(DAY FROM id)=10"],
            [['id' => ['year' => '10']], "EXTRACT(YEAR FROM id)=10"],
            [['id' => ['hour' => '10']], "EXTRACT(HOUR FROM id)=10"],
            [['id' => ['month' => '10']], "EXTRACT(MONTH FROM id)=10"],
            [['id' => ['week_day' => '10']], "EXTRACT(DAYOFWEEK FROM id)=10"],
        ];
    }

    /**
     * @dataProvider lookupProvider
     */
    public function testLookups($where, $whereSql)
    {
        $qb = new QueryBuilder(new Adapter, new LookupBuilder);
        $qb->setSelect('*')->setFrom('tests')->setWhere($where);
        $this->assertEquals($qb->toSQL(), 'SELECT * FROM tests WHERE ' . $whereSql);
    }
}