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
        $adapter = $this->getAdapter();
        $qb = $this->getQueryBuilder()->setTypeInsert()->insert('bool_values', ['bool_col'], [
            [true]
        ]);
        $this->assertEquals($adapter->quoteSql('INSERT INTO [[bool_values]] ([[bool_col]]) VALUES ((TRUE))'), $qb->toSQL());
    }
}