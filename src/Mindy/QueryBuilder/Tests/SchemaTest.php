<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

abstract class SchemaTest extends BaseTest
{
    abstract public function testLimitOffset();

    public function testRandomOrder()
    {
        $adapter = $this->getQueryBuilder()->getAdapter();
        switch ($this->getConnection()->getDriver()->getName()) {
            case 'sqlite':
            case 'pgsql':
                $this->assertEquals('RANDOM()', $adapter->getRandomOrder());
                break;
            case 'mysql':
                $this->assertEquals('RAND()', $adapter->getRandomOrder());
                break;
        }
    }

    public function testDistinct()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('SELECT * FROM [[profile]]', $qb->from('profile')->toSQL());
        $this->assertSql('SELECT DISTINCT [[description]] FROM [[profile]]', $qb->select('description', true)->from('profile')->toSQL());
    }

    public function testGetDateTime()
    {
        $a = $this->getQueryBuilder()->getAdapter();
        $timestamp = strtotime('2016-07-22 13:54:09');
        $this->assertEquals('2016-07-22', $a->getDate($timestamp));
        $this->assertEquals('2016-07-22 13:54:09', $a->getDateTime($timestamp));

        $this->assertEquals('2016-07-22', $a->getDate((string) $timestamp));
        $this->assertEquals('2016-07-22 13:54:09', $a->getDateTime((string) $timestamp));

        $this->assertEquals('2016-07-22', $a->getDate('2016-07-22 13:54:09'));
        $this->assertEquals('2016-07-22 13:54:09', $a->getDateTime('2016-07-22 13:54:09'));
    }
}
