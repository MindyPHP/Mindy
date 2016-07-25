<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Sqlite\Adapter;
use Mindy\QueryBuilder\Exception\NotSupportedException;

class SqliteBuildSchemaTest extends BuildSchemaTest
{
    public function getAdapter()
    {
        return new Adapter();
    }

    public function testRenameColumn($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->renameColumn('profile', 'description', 'title');
    }

    public function testAddPrimaryKey($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->addPrimaryKey('test', 'user_id', ['foo']);
    }

    public function testDropPrimaryKey($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->dropPrimaryKey('test', 'foo');
    }

    public function testAlterColumn($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->alterColumn('test', 'name', 'varchar(255)');
    }

    public function testAddForeignKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->addForeignKey('test', 'foo', ['id'], 'user', 'bar', null, null);
    }

    public function testDropColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->dropColumn('test', 'foo');
    }

    public function testRenameTable($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        $this->getQueryBuilder()->renameColumn('profile', 'description', 'title');
    }

    public function testAddColumn()
    {
        $this->assertSql(
            'ALTER TABLE [[test]] ADD COLUMN [[name]] varchar(255)',
            $this->getQueryBuilder()->addColumn('test', 'name', 'varchar(255)')
        );
    }
}