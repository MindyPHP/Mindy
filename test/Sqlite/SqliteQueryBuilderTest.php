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

class SqliteQueryBuilderTest extends DummyQueryBuilderTest
{
    public function getAdapter()
    {
        return new Adapter();
    }

    public function testRenameColumn($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testAddPrimaryKey($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testAddPrimaryKey($resultSql);
    }

    public function testDropPrimaryKey($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testAddPrimaryKey($resultSql);
    }

    public function testAlterColumn($resultSql = null)
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testAddPrimaryKey($resultSql);
    }

    public function testAddForeignKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testAddForeignKey();
    }

    public function testDropColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testDropColumn();
    }

    public function testDropIndex()
    {
        $a = $this->getAdapter();
        $this->assertEquals($a->quoteSql('DROP INDEX [[name]]'), $a->sqlDropIndex('test', 'name'));
    }

    public function testRenameTable($resultSql = null)
    {
        parent::testRenameTable('ALTER TABLE [[test]] RENAME TO [[foo]]');
    }
}