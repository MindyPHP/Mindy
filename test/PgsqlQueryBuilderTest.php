<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Pgsql\Adapter;

class PgsqlQueryBuilderTest extends DummyQueryBuilderTest
{
    public function getAdapter()
    {
        return new Adapter;
    }

    public function testBoolInsert()
    {
        $qb = $this->getQueryBuilder()->setFrom('bool_values')->setInsert([
            ['bool_col' => true]
        ]);
        $sql = $qb->toSQL();
        $this->assertEquals('INSERT INTO public.bool_values (bool_col) VALUES (TRUE);', $sql);
    }
}