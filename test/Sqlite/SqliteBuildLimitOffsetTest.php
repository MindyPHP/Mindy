<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 19:08
 */

namespace Mindy\QueryBuilder\Test\Sqlite;

use Mindy\QueryBuilder\Database\Sqlite\Adapter;
use Mindy\QueryBuilder\Tests\BuildLimitOffsetTest;

class SqliteBuildLimitOffsetTest extends BuildLimitOffsetTest
{
    public function getAdapter()
    {
        return new Adapter;
    }

    public function testOffset()
    {
        $qb = $this->getQueryBuilder()->offset(10);
        $this->assertSql('LIMIT 9223372036854775807 OFFSET 10', $qb->buildLimitOffset());
    }
}