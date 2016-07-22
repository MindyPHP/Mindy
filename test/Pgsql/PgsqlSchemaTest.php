<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/07/16
 * Time: 11:10
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Mysql\Adapter as MysqlAdapter;
use Mindy\QueryBuilder\Database\Pgsql\Adapter as PgsqlAdapter;
use Mindy\QueryBuilder\Database\Sqlite\Adapter as SqliteAdapter;
use Mindy\QueryBuilder\Tests\SchemaTest;
use PDO;

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

    /**
     * @return PgsqlAdapter|MysqlAdapter|SqliteAdapter
     */
    protected function getAdapter()
    {
        return new PgsqlAdapter();
    }

    /**
     * @return \PDO
     */
    protected function createDriver()
    {
        return new PDO('pgsql:dbname=test;host=localhost', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function testQuoteValue()
    {
        $c = $this->connection;
        $this->assertEquals('FALSE', $c->getAdapter()->quoteValue(false));
        $this->assertEquals('FALSE', $c->getAdapter()->quoteValue('false'));
        $this->assertEquals('TRUE', $c->getAdapter()->quoteValue(true));
        $this->assertEquals('TRUE', $c->getAdapter()->quoteValue('true'));
        $this->assertEquals('NULL', $c->getAdapter()->quoteValue('null'));
        $this->assertEquals('NULL', $c->getAdapter()->quoteValue(null));
    }
}