<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 15/09/16
 * Time: 13:20.
 */

namespace Mindy\QueryBuilder\Tests;

class PgsqlSchemaTest extends SchemaTest
{
    protected $driver = 'pgsql';

    public function testLimitOffset()
    {
        $sql = $this->getQueryBuilder()->from('profile')->offset(1)->toSQL();
        $this->assertEquals($this->quoteSql('SELECT * FROM [[profile]] LIMIT ALL OFFSET 1'), $sql);
    }
}
