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

class BuildSelectTest extends BaseTest
{
    public function testArray()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(['id', 'name']);
        $this->assertEquals($this->quoteSql('[[id]], [[name]]'), $qb->buildColumns());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id, name');
        $this->assertEquals($this->quoteSql('[[id]], [[name]]'), $qb->buildColumns());
    }

    public function testStringWithAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('id AS foo, name AS bar');
        $this->assertEquals($this->quoteSql('[[id]] AS [[foo]], [[name]] AS [[bar]]'), $qb->buildColumns());
    }

    public function testSubSelectString()
    {
        $qb = $this->getQueryBuilder();
        $qb->select('(SELECT [[id]] FROM [[test]]) AS [[id_list]]');
        $this->assertEquals($this->quoteSql('(SELECT [[id]] FROM [[test]]) AS [[id_list]]'), $qb->buildColumns());
    }

    public function testSubSelect()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->select('id')->from('test');

        $qb = $this->getQueryBuilder();
        $qb->select(['id_list' => $qbSub->toSQL()]);
        $this->assertEquals($this->quoteSql('SELECT (SELECT [[id]] FROM [[test]]) AS [[id_list]]'), $qb->buildSelect());
    }

    public function testCount()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(new Count('*', 'test'));
        $this->assertEquals($this->quoteSql('COUNT(*) AS [[test]]'), $qb->buildColumns());

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