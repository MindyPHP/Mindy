<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\QueryBuilder\Tests;

class BuildLimitOffsetTest extends BaseTest
{
    public $limit = '';

    public function testLimit()
    {
        $qb = $this->getQueryBuilder();
        $qb->limit(10);
        $this->assertSql('LIMIT 10', $qb->buildLimitOffset());
    }

    public function testLimitOffset()
    {
        $qb = $this->getQueryBuilder();
        $qb->limit(10);
        $qb->offset(10);
        $this->assertSql('LIMIT 10 OFFSET 10', $qb->buildLimitOffset());
    }

    public function testPaginate()
    {
        $qb = $this->getQueryBuilder();
        $qb->paginate(4, 10);
        $this->assertSql('LIMIT 10 OFFSET 30', $qb->buildLimitOffset());
    }
}
