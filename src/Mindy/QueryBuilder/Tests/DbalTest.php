<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

class DbalTest extends BaseTest
{
    public function testSimple()
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $expr = $qb->expr();
        $where = $expr->andX($expr->orX(
            $expr->eq('u.id', '?1'),
            $expr->like('u.nickname', '?2')
        ))->add($expr->andX(
            $qb->expr()->eq('u.gid', '?3')
        ));
        $qb->select(['a', 'b', 'c'])
            ->add('where', $where)
        ->add('select', ['d', 'e'], true);
        $this->assertEquals('SELECT a, b, c, d, e WHERE ((u.id = ?1) OR (u.nickname LIKE ?2)) AND (u.gid = ?3)', $qb->getSQL());
    }
}
