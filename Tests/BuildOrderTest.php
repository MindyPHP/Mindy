<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 17:04.
 */

namespace Mindy\QueryBuilder\Tests;

class BuildOrderTest extends BaseTest
{
    public function testOrder()
    {
        $qb = $this->getQueryBuilder();
        $qb->order(['id', '-name']);
        $this->assertSql('ORDER BY [[id]] ASC, [[name]] DESC', $qb->buildOrder());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->order('id ASC, name DESC');
        $this->assertSql('ORDER BY [[id]] ASC, [[name]] DESC', $qb->buildOrder());

        $qb = $this->getQueryBuilder();
        $qb->order('id, name');
        $this->assertSql('ORDER BY [[id]], [[name]]', $qb->buildOrder());
    }

    public function testOrderEmpty()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('', $qb->buildOrder());
    }
}
