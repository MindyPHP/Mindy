<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

class PgsqlBuildSchemaTest extends BuildSchemaTest
{
    public function testBoolInsert()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql(
            'INSERT INTO [[bool_values]] ([[bool_col]]) VALUES (TRUE),(NULL)',
            $qb->insert('bool_values', ['bool_col'], [[true], [null]])
        );

        $this->assertSql(
            'INSERT INTO [[bool_values]] ([[bool_col]]) VALUES (TRUE),(FALSE)',
            $qb->insert('bool_values', ['bool_col'], [['true'], ['false']])
        );
    }

    public function testRenameColumn()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('ALTER TABLE [[profile]] RENAME COLUMN [[description]] TO [[title]]',
            $qb->renameColumn('profile', 'description', 'title'));
    }

    public function testRenameTable($resultSql = null)
    {
        $this->assertSql(
            'ALTER TABLE [[test]] RENAME TO [[foo]]',
            $this->getQueryBuilder()->renameTable('test', 'foo')
        );
    }

    public function testConvertToDbValue()
    {
        $a = $this->getAdapter();
        $this->assertEquals('TRUE', $a->convertToDbValue(true));
        $this->assertEquals('FALSE', $a->convertToDbValue(false));
        $this->assertEquals('NULL', $a->convertToDbValue(null));
    }

    public function testAlterColumn()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('ALTER TABLE [[test]] ALTER COLUMN [[name]] TYPE varchar(255)',
            $qb->alterColumn('test', 'name', 'varchar(255)'));
    }

    public function testDropPrimaryKey()
    {
        $this->assertSql(
            'ALTER TABLE [[test]] DROP CONSTRAINT [[user_id]]',
            $this->getQueryBuilder()->dropPrimaryKey('test', 'user_id')
        );
    }
}