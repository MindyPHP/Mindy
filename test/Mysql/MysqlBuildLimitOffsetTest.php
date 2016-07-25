<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 19:08
 */

namespace Mindy\QueryBuilder\Test\Mysql;

use Mindy\QueryBuilder\Database\Mysql\Adapter;
use Mindy\QueryBuilder\Tests\BuildLimitOffsetTest;

class MysqlBuildLimitOffsetTest extends BuildLimitOffsetTest
{
    public function getAdapter()
    {
        return new Adapter;
    }

    public function testOffset()
    {
        $qb = $this->getQueryBuilder()->offset(10);
        $this->assertSql('LIMIT 10, 18446744073709551615', $qb->buildLimitOffset());
    }
}