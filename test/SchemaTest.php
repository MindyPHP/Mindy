<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 30/06/16
 * Time: 10:07
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\QueryBuilderFactory;
use PDO;
use Mindy\QueryBuilder\Database\Pgsql\Adapter as PgsqlAdapter;
use Mindy\QueryBuilder\Database\Mysql\Adapter as MysqlAdapter;
use Mindy\QueryBuilder\Database\Sqlite\Adapter as SqliteAdapter;

abstract class SchemaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryBuilderFactory
     */
    public $factory;

    /**
     * @return PgsqlAdapter|MysqlAdapter|SqliteAdapter
     */
    abstract protected function getAdapter();

    /**
     * @return \PDO
     */
    abstract protected function createDriver();

    protected function setUp()
    {
        parent::setUp();
        $driver = $this->createDriver();

        $adapter = $this->getAdapter();
        $adapter->setDriver($driver);

        $lb = new Legacy($this->getAdapter()->getLookupCollection()->getLookups());

        $this->factory = new QueryBuilderFactory($adapter, $lb);

        $driver->exec(file_get_contents(__DIR__ . '/up.sql'));
    }

    protected function tearDown()
    {
        $this->createDriver()->exec(file_get_contents(__DIR__ . '/down.sql'));
    }

    protected function getQueryBuilder()
    {
        return $this->factory->getQueryBuilder();
    }

    public function testInit()
    {
        $qb = $this->getQueryBuilder();
        $this->createDriver()->exec($qb->addColumn('test', 'user_id', 'int'));
    }
}