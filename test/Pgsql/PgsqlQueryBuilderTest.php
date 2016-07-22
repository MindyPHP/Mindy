<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Pgsql\Adapter;

class PgsqlQueryBuilderTest extends DummyQueryBuilderTest
{
    public function getAdapter()
    {
        return new Adapter();
    }

    public function testBoolInsert()
    {
        $this->markTestSkipped('TODO');
        $qb = $this->getQueryBuilder();
        $qb->setTypeInsert()->insert('bool_values', ['bool_col'], [[true]]);
        $this->assertEquals($qb->getAdapter()->quoteSql('INSERT INTO [[bool_values]] ([[bool_col]]) VALUES ((TRUE))'), $qb->toSQL());
    }

    public function testRenameColumn($resultSql = null)
    {
        parent::testRenameColumn('ALTER TABLE [[test]] RENAME COLUMN [[name]] TO [[title]]');
    }

    public function testRenameTable($resultSql = null)
    {
        parent::testRenameTable('ALTER TABLE [[test]] RENAME TO [[foo]]');
    }

    public function testConvertToDbValue()
    {
        $a = $this->getAdapter();
        $this->assertEquals('TRUE', $a->convertToDbValue(true));
        $this->assertEquals('FALSE', $a->convertToDbValue(false));
        $this->assertEquals('NULL', $a->convertToDbValue(null));
    }

    public function testAlterColumn($resultSql = null)
    {
        parent::testAlterColumn('ALTER TABLE [[test]] ALTER COLUMN [[name]] TYPE varchar(255)');
    }

    public function testAddColumn($resultSql = null)
    {
        parent::testAddColumn('ALTER TABLE [[test]] ADD [[name]] varchar(255)');
    }
}