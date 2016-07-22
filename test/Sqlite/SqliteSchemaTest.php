<?php

namespace Mindy\QueryBuilder\Tests;

use Mindy\Query\Database\Sqlite\PDO;
use Mindy\QueryBuilder\Database\Mysql\Adapter as MysqlAdapter;
use Mindy\QueryBuilder\Database\Pgsql\Adapter as PgsqlAdapter;
use Mindy\QueryBuilder\Database\Sqlite\Adapter as SqliteAdapter;
use Mindy\QueryBuilder\Exception\NotSupportedException;

/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/07/16
 * Time: 14:46
 */
class SqliteSchemaTest extends SchemaTest
{

    /**
     * @return PgsqlAdapter|MysqlAdapter|SqliteAdapter
     */
    protected function getAdapter()
    {
        return new SqliteAdapter;
    }

    /**
     * @return \PDO
     */
    protected function createDriver()
    {
        return new PDO('sqlite::memory:', '', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function testRenameColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testDropColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testForeignKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testDropPrimaryKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testDropForeignKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testDropForeignKey();
    }
}