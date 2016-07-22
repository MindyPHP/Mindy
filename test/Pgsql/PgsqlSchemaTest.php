<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/07/16
 * Time: 11:10
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\Query\Tests\SchemaTest;

class PgsqlSchemaTest extends SchemaTest
{
    public function testAlterColumn()
    {
        $qb = $this->getQueryBuilder();
        $expected = 'ALTER TABLE "foo1" ALTER COLUMN "bar" TYPE varchar(255)';
        $sql = $qb->alterColumn('foo1', 'bar', 'varchar(255)');
        $this->assertEquals($expected, $sql);
        $expected = 'ALTER TABLE "foo1" ALTER COLUMN "bar" SET NOT null';
        $sql = $qb->alterColumn('foo1', 'bar', 'SET NOT null');
        $this->assertEquals($expected, $sql);
        $expected = 'ALTER TABLE "foo1" ALTER COLUMN "bar" drop default';
        $sql = $qb->alterColumn('foo1', 'bar', 'drop default');
        $this->assertEquals($expected, $sql);
        $expected = 'ALTER TABLE "foo1" ALTER COLUMN "bar" reset xyz';
        $sql = $qb->alterColumn('foo1', 'bar', 'reset xyz');
        $this->assertEquals($expected, $sql);
    }
}