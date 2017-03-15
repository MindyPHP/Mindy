<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\QueryBuilder;

class CloneCallback
{
    public function run(QueryBuilder $queryBuilder, $lookupBuilder, $lookupNodes, $value)
    {
        $queryBuilder->join('LEFT JOIN', 'test', ['test_1.id' => 'user_1.user_id'], 'test_1');

        return ['exact', 'id', $value];
    }
}

class CloneTest extends BaseTest
{
    public function testClone()
    {
        $qb = $this->getQueryBuilder();
        $clone = clone $qb;
        $clone->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $this->assertSql('', $qb->buildJoin());

        $qb = $this->getQueryBuilder();
        $clone = clone $qb;
        $qb->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $this->assertSql('', $clone->buildJoin());
    }

    public function testCloneToSql()
    {
        $qb = $this->getQueryBuilder();
        $clone = clone $qb;
        $clone->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $this->assertSql('SELECT *', $qb->toSQL());

        $qb = $this->getQueryBuilder();
        $clone = clone $qb;
        $qb->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $this->assertSql('SELECT *', $clone->toSQL());
    }

    public function testCloneAfterToSql()
    {
        $qb = $this->getQueryBuilder();
        $qb->join('LEFT JOIN', 'test', ['id' => 'user_id']);
        $this->assertSql('LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $qb->buildJoin());
        $sql = $qb->toSQL();
        $clone = clone $qb;
        $this->assertSql('LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $qb->buildJoin());
        $this->assertSql('SELECT * LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $sql);
        $sql = $clone->toSQL();
        $this->assertSql('SELECT * LEFT JOIN [[test]] ON [[id]]=[[user_id]]', $sql);
    }

    public function testCloneCallback()
    {
        $qb = $this->getQueryBuilder();
        $qb->getLookupBuilder()->setCallback(new CloneCallback());
        $qb->from('user')->where(['test__id' => 1])->setAlias('user_1');
        $sql = 'SELECT [[user_1]].* FROM [[user]] AS [[user_1]] LEFT JOIN [[test]] AS [[test_1]] ON [[test_1]].[[id]]=[[user_1]].[[user_id]] WHERE ([[user_1]].[[id]]=1)';

        $clone = clone $qb;
        $this->assertSql($sql, $clone->toSQL());
    }
}
