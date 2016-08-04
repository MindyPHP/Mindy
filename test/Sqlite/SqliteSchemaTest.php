<?php

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Exception\NotSupportedException;

/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/07/16
 * Time: 14:46
 */
class SqliteSchemaTest extends SchemaTest
{
    public function testAddForeignKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testRenameColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testRenameColumn();
    }

    public function testDropColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testDropColumn();
    }

    public function testDropPrimaryKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testDropPrimaryKey();
    }

    public function testDropForeignKey()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testDropForeignKey();
    }

    public function testAlterColumn()
    {
        $this->setExpectedException(NotSupportedException::class, 'not supported by SQLite');
        parent::testAlterColumn();
    }
}