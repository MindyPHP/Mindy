<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Mysql\Adapter;

class MysqlQueryBuilderTest extends DummyQueryBuilderTest
{
    public function getAdapter()
    {
        return new Adapter;
    }

    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals($qb
            ->setSelect('*')
            ->setFrom('tests')
            ->toSQL(), 'SELECT * FROM `tests`');
    }

    public function testOrderGroup()
    {
        $qb = $this->getQueryBuilder();
        $qb->setSelect('*')->setFrom('comment')->setOrder(['id'])->setGroup(['id']);
        $this->assertEquals($qb->toSQL(), 'SELECT * FROM `comment` GROUP BY `id` ORDER BY `id` ASC');

        $qb->clear();
        $qb->setSelect('*')->setAlias('t')->setFrom('comment')->setOrder(['id'])->setGroup(['id']);
        $this->assertEquals($qb->toSQL(), 'SELECT `t`.* FROM `comment` AS `t` GROUP BY `t`.`id` ORDER BY `t`.`id` ASC');
    }

    public function testAlias()
    {
        $qb = $this->getQueryBuilder();
        $qb->setSelect('*')->setFrom('comment')
            ->setAlias('t')
            ->setJoin('LEFT JOIN', 'user', ['t.user_id' => 'u.id'], 'u');
        $this->assertEquals($qb->toSQL(), 'SELECT `t`.* FROM `comment` AS `t` LEFT JOIN `user` AS `u` ON `t`.`user_id`=`u`.`id`');

        $qb = $this->getQueryBuilder();
        $qb->setSelect('t.id AS foo, t.user_id AS bar')->setFrom('comment')
            ->setAlias('t')
            ->setJoin('LEFT JOIN', 'user', ['t.user_id' => 'u.id'], 'u');
        $this->assertEquals($qb->toSQL(), 'SELECT `t`.`id` AS `foo`,`t`.`user_id` AS `bar` FROM `comment` AS `t` LEFT JOIN `user` AS `u` ON `t`.`user_id`=`u`.`id`');
    }

    public function testJoin()
    {
        /**
         * user: id, group_id
         * comment: id, user_id, is_published
         * group: id
         */
        $qb = $this->getQueryBuilder();
        $sql = $qb->setSelect('*')->setFrom('comment')->setJoin('LEFT JOIN', 'user', [
            'user_id' => 'id'
        ])->toSQL();
        $this->assertEquals($sql, 'SELECT * FROM `comment` LEFT JOIN `user` ON `user_id`=`id`');

        $qb->clear();
        $sql = $qb->setSelect('*')->setFrom('comment')
            ->setJoin('LEFT JOIN', 'user', ['user_id' => 'id'])
            ->setJoin('LEFT JOIN', 'group', ['group_id' => 'id'])
            ->toSQL();
        $this->assertEquals($sql, 'SELECT * FROM `comment` LEFT JOIN `user` ON `user_id`=`id` LEFT JOIN `group` ON `group_id`=`id`');
    }

    public function testInsert()
    {
        $qb = $this->getQueryBuilder();
        $qb->setTypeInsert()->setAlias('t')->setInsert([
            ['name' => 'qwe']
        ])->setFrom('test');
        $this->assertEquals($qb->toSQL(), "INSERT INTO `test` (`name`) VALUES ('qwe')");
    }

    public function testUpdate()
    {
        $qb = $this->getQueryBuilder();
        $qb->setTypeUpdate()->setUpdate(['name' => 'bar'])->setWhere(['name' => 'foo'])->setFrom('test');
        $this->assertEquals($qb->toSQL(), "UPDATE `test` SET `name`='bar' WHERE `name`='foo'");
    }

    public function testDelete()
    {
        $qb = $this->getQueryBuilder();
        $qb->setTypeDelete()->setWhere(['name' => 'qwe'])->setFrom('test');
        $this->assertEquals($qb->toSQL(), "DELETE FROM `test` WHERE `name`='qwe'");
    }

    public function testSubQuerySelect()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->setTypeSelect()->setFrom('comment')->setSelect('*')->setWhere(['is_published' => true]);
        $qb = $this->getQueryBuilder();
        $qb->setTypeSelect()->setSelect(['id' => $qbSub->toSQL()])->setFrom('test');
        $this->assertEquals($qb->toSQL(), 'SELECT (SELECT * FROM `comment` WHERE `is_published`=1) AS `id` FROM `test`');
    }

    public function testSubQueryFrom()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->setTypeSelect()->setFrom('comment')->setSelect('user_id')->setWhere(['is_published' => true]);
        $qb = $this->getQueryBuilder();
        $qb->setTypeSelect()->setAlias('t')->setSelect(['user_id'])->setFrom($qbSub->toSQL());
        $this->assertEquals($qb->toSQL(), 'SELECT `t`.`user_id` FROM (SELECT `user_id` FROM `comment` WHERE `is_published`=1) AS `t`');
    }

    public function testSubQueryJoin()
    {
        $qbSub = $this->getQueryBuilder();
        $qbSub->setTypeSelect()->setFrom('user')->setSelect('id');
        $qb = $this->getQueryBuilder();
        $qb->setTypeSelect()->setAlias('c')->setSelect(['*'])->setFrom('comment')
            ->setJoin('INNER JOIN', $qbSub->toSQL(), ['u.id' => 'c.user_id'], 'u');

        $this->assertEquals($qb->toSQL(), 'SELECT `c`.* FROM `comment` AS `c` INNER JOIN (SELECT `id` FROM `user`) AS `u` ON `u`.`id`=`c`.`user_id`');
    }
}