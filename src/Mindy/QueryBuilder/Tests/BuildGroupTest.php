<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

class BuildGroupTest extends BaseTest
{
    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $qb->group(['id', 'name']);
        $this->assertSql('GROUP BY [[id]], [[name]]', $qb->buildGroup());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->group('id, name');
        $this->assertSql('GROUP BY [[id]], [[name]]', $qb->buildGroup());
    }

    public function testOrderEmpty()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('', $qb->buildOrder());
    }
}
