<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:00
 */

namespace Mindy\QueryBuilder\Tests;

use Adapter;
use Mindy\QueryBuilder\LegacyLookupBuilder;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\QueryBuilder;
use Mindy\QueryBuilder\Sqlite\LookupCollection;

class LegacyLookupBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function lookupProvider()
    {
        return [
            [['id' => 1], 'id=1'],
            [['id__exact' => 1], 'id=1'],
            [['id__gte' => 1], 'id>=1'],
            [['id__lte' => 1], 'id<=1'],
            [['id__gt' => 1], 'id>1'],
            [['id__lt' => 1], 'id<1'],
            [['id__range' => [1, 2]], 'id BETWEEN 1 AND 2'],
            [['id__isnull' => true], 'id IS NULL'],
            [['id__isnull' => false], 'id IS NOT NULL'],
            [['id__contains' => 'FOO'], 'id LIKE %FOO%'],
            [['id__icontains' => 'FOO'], 'LOWER(id) LIKE %foo%'],
            [['id__startswith' => 'FOO'], 'id LIKE FOO%'],
            [['id__istartswith' => 'FOO'], 'LOWER(id) LIKE foo%'],
            [['id__endswith' => 'FOO'], 'id LIKE %FOO'],
            [['id__iendswith' => 'FOO'], 'LOWER(id) LIKE %foo'],
            [['id__in' => [1, 2]], 'id IN (1,2)'],
            [['id__raw' => "REGEXP '^[abc]'"], "id REGEXP '^[abc]'"],
            [['id__regex' => '^[abc]'], "BINARY id REGEXP ^[abc]"],
            [['id__iregex' => '^[abc]'], "id REGEXP ^[abc]"],
            [['id__second' => '10'], "EXTRACT(SECOND FROM id)=10"],
            [['id__day' => '10'], "EXTRACT(DAY FROM id)=10"],
            [['id__year' => '10'], "EXTRACT(YEAR FROM id)=10"],
            [['id__hour' => '10'], "EXTRACT(HOUR FROM id)=10"],
            [['id__month' => '10'], "EXTRACT(MONTH FROM id)=10"],
            [['id__week_day' => '10'], "EXTRACT(DAYOFWEEK FROM id)=10"],
        ];
    }

    /**
     * @dataProvider lookupProvider
     */
    public function testLookups($where, $whereSql)
    {
        $collection = new LookupCollection();
        $qb = new QueryBuilder(new Adapter, new Legacy($collection->getLookups()));
        $qb->setSelect('*')->setFrom('tests')->setWhere($where);
        $this->assertEquals($qb->toSQL(), 'SELECT * FROM tests WHERE ' . $whereSql);
    }
}