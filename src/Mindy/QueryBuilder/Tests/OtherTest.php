<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

class OtherTest extends BaseTest
{
    public function testConvertToDbValue()
    {
        $a = $this->getAdapter();
        $this->assertEquals('1', $a->convertToDbValue(true));
        $this->assertEquals('0', $a->convertToDbValue(false));
        $this->assertEquals('NULL', $a->convertToDbValue(null));
    }

    public function testGroupOrder()
    {
        // Проверка порядка генерирования ORDER BY и GROUP BY
        $qb = $this->getQueryBuilder();
        $qb->select('t.*')->from(['t' => 'comment'])->group(['t.id'])->order(['t.id']);
        $this->assertSql(
            'SELECT [[t]].* FROM [[comment]] AS [[t]] GROUP BY [[t]].[[id]] ORDER BY [[t]].[[id]] ASC',
            $qb->toSQL()
        );
    }

    public function testClone()
    {
        $sql = $this->getAdapter()->quoteSql('SELECT [[a]], [[b]], [[c]] FROM [[test]]');

        $qb = $this->getQueryBuilder();
        $qb->select('a, b, c')->from('test');

        $this->assertEquals($sql, $qb->toSQL());
        $copy = clone $qb;
        $this->assertEquals($sql, $copy->toSQL());
    }

    public function testRawTableName()
    {
        $this->assertEquals('test', $this->getAdapter()->getRawTableName('{{%test}}'));
        $this->assertEquals('test', $this->getAdapter()->getRawTableName('test'));
    }

    public function testInsert()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals(
            $this->quoteSql('INSERT INTO [[test]] ([[name]]) VALUES (@qwe@)'),
            $qb->insert('test', [['name' => 'qwe']])
        );
        $this->assertEquals(
            $this->quoteSql('INSERT INTO [[test]] ([[name]]) VALUES (@foo@), (@bar@)'),
            $qb->insert('test', [['name' => 'foo'], ['name' => 'bar']])
        );
    }

    public function testUpdate()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals(
            $this->quoteSql('UPDATE [[test]] SET [[name]]=@bar@ WHERE ([[name]]=@foo@)'),
            $qb->setTypeUpdate()->update('test', ['name' => 'bar'])->where(['name' => 'foo'])->toSQL()
        );
    }

    public function testDelete()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals(
            $this->quoteSql('DELETE FROM [[test]] WHERE ([[name]]=@qwe@)'),
            $qb->setTypeDelete()->where(['name' => 'qwe'])->from('test')->toSQL()
        );
    }
}
