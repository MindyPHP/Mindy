<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 17:04.
 */

namespace Mindy\QueryBuilder\Tests;

class BuildGroupTest extends BaseTest
{
    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $qb->group(['id', 'name']);
        $this->assertSql('GROUP BY [[id]], [[name]]', $qb->buildGroup());
    }

    public function testString()
    {
        $qb = $this->getQueryBuilder();
        $qb->group('id, name');
        $this->assertSql('GROUP BY [[id]], [[name]]', $qb->buildGroup());
    }

    public function testOrderEmpty()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('', $qb->buildOrder());
    }
}
