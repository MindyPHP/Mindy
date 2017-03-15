<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Mindy\Query\Connection;
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
        $dbConfig = require $dir.'/'.(@getenv('TRAVIS') ? 'config_travis.php' : 'config.php');

        $fixtures = $dbConfig['fixture'];
        unset($dbConfig['fixture']);

        $config = new Configuration();
        $this->connection = DriverManager::getConnection($dbConfig, $config);

        $this->loadFixtures($this->connection, $fixtures);

        $this->config = $dbConfig;
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
            'id' => 'int(11)',
        ], ''));

        $this->assertEquals($this->quoteSql('CREATE TABLE [[test]] (
	[[id]] int(11)
) CHARACTER SET utf8 COLLATE utf8_bin'), $qb->createTable('test', [
            'id' => 'int(11)',
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
