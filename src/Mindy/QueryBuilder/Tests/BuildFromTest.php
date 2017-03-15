<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

class BuildFromTest extends BaseTest
{
    public function testAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->from(['test' => 'foo', 'bar']);
        $this->assertSql('FROM [[foo]] AS [[test]], [[bar]]', $qb->buildFrom());

        $qb = $this->getQueryBuilder();
        $qb->setAlias('test')->from('foo');
        $this->assertSql('FROM [[foo]] AS [[test]]', $qb->buildFrom());
    }

    public function testArray()
    {
        $qb = $this->getQueryBuilder();
        $qb->from(['foo', 'bar']);
        $this->assertSql('FROM [[foo]], [[bar]]', $qb->buildFrom());
    }

    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $qb->from('test');
        $this->assertSql('FROM [[test]]', $qb->buildFrom());
    }

    public function testSubSelectString()
    {
        $result = 'FROM (SELECT [[user_id]] FROM [[comment]] WHERE ([[name]]=@foo@)) AS [[t]]';

        $qbSub = $this->getQueryBuilder();
        $qbSub->from(['comment'])->select('user_id')->where(['name' => 'foo']);

        $qb = $this->getQueryBuilder()->from(['t' => $qbSub->toSQL()]);
        $this->assertSql($result, $qb->buildFrom());
    }

    public function testSubSelect()
    {
        $result = 'FROM (SELECT [[user_id]] FROM [[comment]] WHERE ([[name]]=@foo@)) AS [[t]]';

        $qbSub = $this->getQueryBuilder();
        $qbSub->from(['comment'])->select('user_id')->where(['name' => 'foo']);

        $qb = $this->getQueryBuilder()->from(['t' => $qbSub]);
        $this->assertSql($result, $qb->buildFrom());
    }
}
