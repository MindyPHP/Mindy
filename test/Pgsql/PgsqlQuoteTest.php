<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 20:38
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Pgsql\Adapter;
use PDO;

class PgsqlQuoteTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('pdo') || !extension_loaded('pdo_pgsql')) {
            $this->markTestSkipped('pdo and pdo_pgsql extension are required.');
        }
    }

    protected function getAdapter()
    {
        try {
            return new Adapter(new PDO('pgsql:dbname=test;host=localhost'));
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function testAutoQuoting()
    {
        $sql = 'SELECT [[id]], [[t.name]] FROM {{customer}} t';
        $this->assertEquals('SELECT "id", "t"."name" FROM "customer" t', $this->getAdapter()->quoteSql($sql));
    }

    public function testQuoteValue()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals(123, $adapter->quoteValue(123));
        $this->assertEquals("'string'", $adapter->quoteValue('string'));
        $this->assertEquals("'It''s interesting'", $adapter->quoteValue("It's interesting"));
    }

    public function testQuoteTableName()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals('"table"', $adapter->quoteTableName('table'));
        $this->assertEquals('"table"', $adapter->quoteTableName('"table"'));
        $this->assertEquals('"schema"."table"', $adapter->quoteTableName('schema.table'));
        $this->assertEquals('"schema"."table"', $adapter->quoteTableName('schema."table"'));
        $this->assertEquals('"schema"."table"', $adapter->quoteTableName('"schema"."table"'));
        $this->assertEquals('{{table}}', $adapter->quoteTableName('{{table}}'));
        $this->assertEquals('(table)', $adapter->quoteTableName('(table)'));
    }

    public function testQuoteColumnName()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals('"column"', $adapter->quoteColumn('column'));
        $this->assertEquals('"column"', $adapter->quoteColumn('"column"'));
        $this->assertEquals('"table"."column"', $adapter->quoteColumn('table.column'));
        $this->assertEquals('"table"."column"', $adapter->quoteColumn('table."column"'));
        $this->assertEquals('"table"."column"', $adapter->quoteColumn('"table"."column"'));
        $this->assertEquals('[[column]]', $adapter->quoteColumn('[[column]]'));
        $this->assertEquals('{{column}}', $adapter->quoteColumn('{{column}}'));
        $this->assertEquals('(column)', $adapter->quoteColumn('(column)'));
    }
}