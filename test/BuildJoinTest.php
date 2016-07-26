<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 16:56
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Expression;

class BuildJoinTest extends BaseTest
{
    public function testAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->join('LEFT JOIN', 'test', ['main.user_id' => 'test_user.id'], 'test_user');
        $this->assertSql('LEFT JOIN [[test]] AS [[test_user]] ON [[main]].[[user_id]]=[[test_user]].[[id]]', $qb->buildJoin());
        $this->assertTrue($qb->hasJoin('test'));
    }

    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $qb->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $this->assertSql('LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $qb->buildJoin());
    }

    public function testSimpleClone()
    {
        $qb = $this->getQueryBuilder();
        $qb->join('LEFT JOIN', 'test', ['id' => 'user_id']);

        $clone = clone $qb;
        $this->assertSql('LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $clone->buildJoin());
    }

    public function testMultiple()
    {
        $qb = $this->getQueryBuilder();
        $qb->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $qb->join('INNER JOIN', 'user', ['parent_id' => 'id']);
        $this->assertSql('LEFT JOIN [[test]] ON [[id]]=[[user_id]] INNER JOIN [[user]] ON [[parent_id]]=[[id]]', $qb->buildJoin());
    }

    public function testRaw()
    {
        $qb = $this->getQueryBuilder();
        $qb->joinRaw('LEFT JOIN [[test]] ON [[id]]=[[user_id]]');
        $this->assertSql('LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $qb->buildJoin());
    }

    public function testExpression()
    {
        $qb = $this->getQueryBuilder();
        $qb->join('LEFT JOIN', 'test', ['user_id' => new Expression('1')]);
        $this->assertSql('LEFT JOIN [[test]] ON [[user_id]]=1', $qb->buildJoin());
    }

    public function testJoinSubSelectString()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->from('user')->select('id');

        $qb = $this->getQueryBuilder();
        $this->assertSql(
            'SELECT [[c]].* FROM [[comment]] AS [[c]] INNER JOIN (SELECT [[id]] FROM [[user]]) AS [[u]] ON [[u]].[[id]]=[[c]].[[user_id]]',
            $qb->select(['c.*'])->from(['c' => 'comment'])
                ->join('INNER JOIN', $qbSub->toSQL(), ['u.id' => 'c.user_id'], 'u')->toSQL()
        );
    }

    public function testJoinSubSelect()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->from('user')->select('id');

        $qb = $this->getQueryBuilder();
        $this->assertSql(
            'SELECT [[c]].* FROM [[comment]] AS [[c]] INNER JOIN (SELECT [[id]] FROM [[user]]) AS [[u]] ON [[u]].[[id]]=[[c]].[[user_id]]',
            $qb->select(['c.*'])->from(['c' => 'comment'])
                ->join('INNER JOIN', $qbSub, ['u.id' => 'c.user_id'], 'u')->toSQL()
        );
    }
}