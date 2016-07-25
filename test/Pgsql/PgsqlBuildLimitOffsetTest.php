<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 19:08
 */

namespace Mindy\QueryBuilder\Test\Pgsql;

use Mindy\QueryBuilder\Database\Pgsql\Adapter;
use Mindy\QueryBuilder\Tests\BuildLimitOffsetTest;

class PgsqlBuildLimitOffsetTest extends BuildLimitOffsetTest
{
    public function getAdapter()
    {
        return new Adapter;
    }

    public function testOffset()
    {
        $qb = $this->getQueryBuilder()->offset(10);
        $this->assertSql('LIMIT ALL OFFSET 10', $qb->buildLimitOffset());
    }
}