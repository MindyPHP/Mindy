<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 16:40
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Aggregation\Avg;
use Mindy\QueryBuilder\Aggregation\Count;
use Mindy\QueryBuilder\Aggregation\Max;
use Mindy\QueryBuilder\Aggregation\Min;
use Mindy\QueryBuilder\Aggregation\Sum;
use Mindy\QueryBuilder\Expression;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\QueryBuilder;

class BuildSelectTest extends BaseTest
{
    public function testSelectExpression()
    {
        $qb = $this->getQueryBuilder();
        $qb->select([
            'id', 'root', 'lft', 'rgt',
            new Expression('[[rgt]]-[[lft]]-1 AS [[move]]')
        ]);
        $this->assertSql('[[id]], [[root]], [[lft]], [[rgt]], [[rgt]]-[[lft]]-1 AS [[move]]', $qb->buildColumns());
    }

    public function testArray()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(['id', 'name']);
        $this->assertSql($this->quoteSql('[[id]], [[name]]'), $qb->buildColumns());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id, name');
        $this->assertSql('[[id]], [[name]]', $qb->buildColumns());
    }

    public function testMultiple()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id');
        $this->assertSql('[[id]]', $qb->buildColumns());
        $qb->select('name');
        $this->assertSql('[[name]]', $qb->buildColumns());
    }

    public function testStringWithAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id AS foo, name AS bar');
        $this->assertSql('[[id]] AS [[foo]], [[name]] AS [[bar]]', $qb->buildColumns());
    }

    public function testSubSelectString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('(SELECT [[id]] FROM [[test]]) AS [[id_list]]');
        $this->assertSql('(SELECT [[id]] FROM [[test]]) AS [[id_list]]', $qb->buildColumns());
    }

    public function testAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->setAlias('test1')->select(['id'])->from('test');
        $this->assertSql('SELECT [[test1]].[[id]]', $qb->buildSelect());
    }

    public function testAliasBackward()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(['id'])->from('test')->setAlias('test1');
        $this->assertSql('SELECT [[test1]].[[id]]', $qb->buildSelect());
    }

    public function testAliasFromString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id')->from('test')->setAlias('test1');
        $this->assertSql('SELECT [[test1]].[[id]]', $qb->buildSelect());
    }

    public function testSubSelect()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->select('id')->from('test');

        $qb = $this->getQueryBuilder();
        $qb->select([$qbSub->toSQL()]);
        $this->assertSql(
            'SELECT (SELECT [[id]] FROM [[test]])',
            $qb->buildSelect()
        );
    }

    public function testSubSelectAlias()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->select('id')->from('test');

        $qb = $this->getQueryBuilder();
        $qb->select(['id_list' => $qbSub->toSQL()]);
        $this->assertSql(
            'SELECT (SELECT [[id]] FROM [[test]]) AS [[id_list]]',
            $qb->buildSelect()
        );
    }

    public function testSelectAutoJoin()
    {
        $qb = $this->getQueryBuilder();
        $qb->getLookupBuilder()->setJoinCallback(function (QueryBuilder $qb, Legacy $lookupBuilder, array $lookupNodes) {
            $column = '';
            $alias = '';
            foreach ($lookupNodes as $i => $nodeName) {
                if ($i + 1 == count($lookupNodes)) {
                    $column = $nodeName;
                } else {
                    switch ($nodeName) {
                        case 'user':
                            $alias = 'user1';
                            $qb->join('LEFT JOIN', $nodeName, ['user1.id' => 'customer.user_id'], $alias);
                            break;
                    }
                }
            }

            if (empty($alias) || empty($column)) {
                return false;
            }

            return [$alias, $column];
        });
        $qb->select(['user__username'])->from('customer');

        $this->assertSql(
            'SELECT [[user1]].[[username]] AS [[user__username]] FROM [[customer]] LEFT JOIN [[user]] AS [[user1]] ON [[user1]].[[id]]=[[customer]].[[user_id]]',
            $qb->toSQL()
        );
    }

    public function testCount()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Count('*', 'test'));
        $this->assertSql('COUNT(*) AS [[test]]', $qb->buildColumns());

        $qb = $this->getQueryBuilder();
        $qb->select(new Count('*'));
        $this->assertEquals('COUNT(*)', $qb->buildColumns());
    }

    public function testAvg()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Avg('*'));
        $this->assertEquals('AVG(*)', $qb->buildColumns());
    }

    public function testSum()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Sum('*'));
        $this->assertEquals('SUM(*)', $qb->buildColumns());
    }

    public function testMin()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Min('*'));
        $this->assertEquals('MIN(*)', $qb->buildColumns());
    }

    public function testMax()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Max('*'));
        $this->assertEquals('MAX(*)', $qb->buildColumns());
    }

    public function testSelect()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals('SELECT *', $qb->buildSelect());
    }

    public function testSelectDistinct()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(null, true);
        $this->assertEquals('SELECT DISTINCT *', $qb->buildSelect());
    }
}