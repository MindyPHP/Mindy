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
        $qb = $this->getQueryBuilder();
        $sql = $qb->insert('bool_values', ['bool_col'], [[true], [null]]);
        $this->assertEquals($qb->getAdapter()->quoteSql('INSERT INTO [[bool_values]] ([[bool_col]]) VALUES (TRUE),(NULL)'), $sql);

        $sql = $qb->insert('bool_values', ['bool_col'], [['true'], ['false']]);
        $this->assertEquals($qb->getAdapter()->quoteSql('INSERT INTO [[bool_values]] ([[bool_col]]) VALUES (TRUE),(FALSE)'), $sql);
    }

    public function testRenameColumn($resultSql = null)
    {
        parent::testRenameColumn('ALTER TABLE [[profile]] RENAME COLUMN [[description]] TO [[title]]');
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

    public function testDropPrimaryKey($resultSql = null)
    {
        parent::testDropPrimaryKey('ALTER TABLE [[test]] DROP CONSTRAINT [[user_id]]');
    }

    public function testDropIndex()
    {
        $a = $this->getAdapter();
        $this->assertEquals($a->quoteSql('DROP INDEX [[name]]'), $a->sqlDropIndex('test', 'name'));
    }
}