<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Aggregation\Avg;
use Mindy\QueryBuilder\Aggregation\Count;
use Mindy\QueryBuilder\Aggregation\Max;
use Mindy\QueryBuilder\Aggregation\Min;
use Mindy\QueryBuilder\Aggregation\Sum;
use Mindy\QueryBuilder\Expression;
use Mindy\QueryBuilder\LookupBuilder\LookupBuilder;
use Mindy\QueryBuilder\QueryBuilder;

class BuildSelectJoinCallback
{
    public function run(QueryBuilder $qb, LookupBuilder $lookupBuilder, array $lookupNodes)
    {
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
    }
}

class BuildSelectTest extends BaseTest
{
    public function testSelectExpression()
    {
        $qb = $this->getQueryBuilder();
        $qb->select([
            'id', 'root', 'lft', 'rgt',
            new Expression('[[rgt]]-[[lft]]-1 AS [[move]]'),
        ]);
        $this->assertSql('SELECT [[id]], [[root]], [[lft]], [[rgt]], [[rgt]]-[[lft]]-1 AS [[move]]', $qb->buildSelect());
    }

    public function testArray()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(['id', 'name']);
        $this->assertSql($this->quoteSql('SELECT [[id]], [[name]]'), $qb->buildSelect());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id, name');
        $this->assertSql('SELECT [[id]], [[name]]', $qb->buildSelect());
    }

    public function testMultiple()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id');
        $this->assertSql('SELECT [[id]]', $qb->buildSelect());
        $qb->select('name');
        $this->assertSql('SELECT [[name]]', $qb->buildSelect());
    }

    public function testStringWithAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id AS foo, name AS bar');
        $this->assertSql('SELECT [[id]] AS [[foo]], [[name]] AS [[bar]]', $qb->buildSelect());
    }

    public function testSubSelectString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('(SELECT [[id]] FROM [[test]]) AS [[id_list]]');
        $this->assertSql('SELECT (SELECT [[id]] FROM [[test]]) AS [[id_list]]', $qb->buildSelect());
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
        $qb->select(['test' => $qbSub->toSQL()]);
        $this->assertSql(
            'SELECT (SELECT [[id]] FROM [[test]]) AS [[test]]',
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
        $qb->getLookupBuilder()->setJoinCallback(new BuildSelectJoinCallback());
        $qb->select(['user__username'])->from('customer');

        $this->assertSql(
            'SELECT [[user1]].[[username]] FROM [[customer]] LEFT JOIN [[user]] AS [[user1]] ON [[user1]].[[id]]=[[customer]].[[user_id]]',
            $qb->toSQL()
        );
    }

    public function testCount()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Count('*', 'test'));
        $this->assertSql('SELECT COUNT(*) AS [[test]]', $qb->buildSelect());

        $qb = $this->getQueryBuilder();
        $qb->select(new Count('*'));
        $this->assertEquals('SELECT COUNT(*)', $qb->buildSelect());
    }

    public function testAvg()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Avg('*'));
        $this->assertEquals('SELECT AVG(*)', $qb->buildSelect());
    }

    public function testSum()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Sum('*'));
        $this->assertEquals('SELECT SUM(*)', $qb->buildSelect());
    }

    public function testMin()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Min('*'));
        $this->assertEquals('SELECT MIN(*)', $qb->buildSelect());
    }

    public function testMax()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Max('*'));
        $this->assertEquals('SELECT MAX(*)', $qb->buildSelect());
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
