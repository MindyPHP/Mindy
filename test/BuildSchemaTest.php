<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 17:34
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\Helper\Creator;
use Mindy\Query\Connection;
use Mindy\Query\PDO;
use ReflectionClass;

abstract class BuildSchemaTest extends BaseTest
{
    protected $config = [];
    /**
     * @var Connection
     */
    protected $connection;

    protected function setUp()
    {
        parent::setUp();
        $reflector = new ReflectionClass(get_class($this));
        $dir = dirname($reflector->getFileName());
        $configFile = @getenv('TRAVIS') ? 'config_travis.php' : 'config.php';
        $this->config =  require($dir . '/' . $configFile);

        try {
            new PDO($this->config['dsn'], $this->config['username'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->connection = Creator::createObject($this->config);
    }

    /**
     * @return \Mindy\QueryBuilder\BaseAdapter
     */
    protected function getAdapter()
    {
        return $this->connection->getAdapter();
    }

    /**
     * @return \Mindy\QueryBuilder\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->connection->getQueryBuilder();
    }

    abstract public function testRenameTable();

    abstract public function testAlterColumn();

    abstract public function testDropPrimaryKey();

    abstract public function testRenameColumn();

    public function testCreateTable()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals($this->quoteSql('CREATE TABLE [[test]] (
	[[id]] int(11)
)'), $qb->createTable('test', [
            'id' => 'int(11)'
        ], ''));

        $this->assertEquals($this->quoteSql('CREATE TABLE [[test]] (
	[[id]] int(11)
) CHARACTER SET utf8 COLLATE utf8_bin'), $qb->createTable('test', [
            'id' => 'int(11)'
        ], 'CHARACTER SET utf8 COLLATE utf8_bin'));

        $this->assertEquals($this->quoteSql('CREATE TABLE [[test]] SELECT * FROM [[clone]]'),
            $qb->createTable('test', 'SELECT * FROM [[clone]]', ''));

        $this->assertEquals($this->quoteSql('CREATE TABLE [[test]] LIKE [[clone]]'),
            $qb->createTable('test', 'LIKE [[clone]]', ''));

        $this->assertEquals($this->quoteSql('CREATE TABLE IF NOT EXISTS [[test]] LIKE [[clone]]'),
            $qb->createTable('test', 'LIKE [[clone]]', '', true));

        $this->assertEquals($this->quoteSql('CREATE TABLE IF NOT EXISTS [[test]] (
	[[id]] int(11)
)'), $qb->createTable('test', ['id' => 'int(11)'], '', true));
    }

    public function testDropTable()
    {
        $qb = $this->getQueryBuilder();
        $this->assertEquals($this->quoteSql('DROP TABLE [[test]]'), $qb->dropTable('test'));
    }

    public function testCreateIndex()
    {
        $this->assertSql(
            'CREATE INDEX [[idx_name]] ON [[test]] ([[name]])',
            $this->getQueryBuilder()->createIndex('test', 'idx_name', ['name'], false)
        );
    }

    public function testAddColumn()
    {
        $this->assertSql(
            'ALTER TABLE [[test]] ADD [[name]] varchar(255)',
            $this->getQueryBuilder()->addColumn('test', 'name', 'varchar(255)')
        );
    }

    public function testAddForeignKey()
    {
        /*
         * $tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null
         */
        $this->assertSql(
            'ALTER TABLE [[test]] ADD CONSTRAINT [[name]] FOREIGN KEY ([[fk_qwe]]) REFERENCES [[foo]] ([[bar]]) ON DELETE SET NULL ON UPDATE SET NULL',
            $this->getQueryBuilder()->addForeignKey('test', 'name', 'fk_qwe', 'foo', 'bar', 'SET NULL', 'SET NULL')
        );
    }

    public function testDropColumn()
    {
        $this->assertSql(
            'ALTER TABLE [[test]] DROP COLUMN [[name]]',
            $this->getQueryBuilder()->dropColumn('test', 'name')
        );
    }

    public function testDropIndex()
    {
        $this->assertSql(
            'DROP INDEX [[name]]',
            $this->getQueryBuilder()->dropIndex('test', 'name')
        );
    }

    public function testAddPrimaryKey()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('ALTER TABLE [[test]] ADD CONSTRAINT [[user_id]] PRIMARY KEY ([[foo]])',
            $qb->addPrimaryKey('test', 'user_id', 'foo'));
    }
}