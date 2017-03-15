<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

class BuildOrderTest extends BaseTest
{
    public function testOrder()
    {
        $qb = $this->getQueryBuilder();
        $qb->order(['id', '-name']);
        $this->assertSql('ORDER BY [[id]] ASC, [[name]] DESC', $qb->buildOrder());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->order('id ASC, name DESC');
        $this->assertSql('ORDER BY [[id]] ASC, [[name]] DESC', $qb->buildOrder());

        $qb = $this->getQueryBuilder();
        $qb->order('id, name');
        $this->assertSql('ORDER BY [[id]], [[name]]', $qb->buildOrder());
    }

    public function testOrderEmpty()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('', $qb->buildOrder());
    }
}
