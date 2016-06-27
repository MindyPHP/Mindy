<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 19:46
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Sqlite\Adapter;
use PDO;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    protected function getAdapter()
    {
        $pdo = new PDO('mysql:root@localhost');
        return new Adapter($pdo);
    }

    public function testQuoteValue()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals(123, $adapter->quoteValue(123));
        $this->assertEquals("'string'", $adapter->quoteValue('string'));
        // Sqlite3
        // A string constant is formed by enclosing the string in single quotes (').
        // A single quote within the string can be encoded by putting two single
        // quotes in a row - as in Pascal. C-style escapes using the backslash
        // character are not supported because they are not standard SQL.
        $this->assertEquals("'It\\'s interesting'", $adapter->quoteValue("It's interesting"));
    }

    public function testQuoteTableName()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals('`table`', $adapter->quoteTableName('table'));
        $this->assertEquals('`table`', $adapter->quoteTableName('`table`'));
        $this->assertEquals('`schema`.`table`', $adapter->quoteTableName('schema.table'));
        $this->assertEquals('`schema`.`table`', $adapter->quoteTableName('schema.`table`'));
        $this->assertEquals('{{table}}', $adapter->quoteTableName('{{table}}'));
        $this->assertEquals('(table)', $adapter->quoteTableName('(table)'));
    }

    public function testQuoteColumnName()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals('`column`', $adapter->quoteColumn('column'));
        $this->assertEquals('`column`', $adapter->quoteColumn('`column`'));
        $this->assertEquals('`table`.`column`', $adapter->quoteColumn('table.column'));
        $this->assertEquals('`table`.`column`', $adapter->quoteColumn('table.`column`'));
        $this->assertEquals('[[column]]', $adapter->quoteColumn('[[column]]'));
        $this->assertEquals('{{column}}', $adapter->quoteColumn('{{column}}'));
        $this->assertEquals('(column)', $adapter->quoteColumn('(column)'));
    }
}