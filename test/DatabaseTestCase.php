<?php
/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 06/01/14.01.2014 17:18
 */

use Mindy\Helper\Creator;
use Mindy\Query\Connection;
use Mindy\Query\ConnectionManager;
use Mindy\QueryBuilder\LegacyLookupBuilder;
use Mindy\QueryBuilder\QueryBuilder;

class DatabaseTestCase extends TestCase
{
    public static $params;

    protected $database;

    protected $driverName = 'mysql';

    /**
     * @var Connection
     */
    protected $db;

    public function setUp()
    {
        parent::setUp();

        if (is_file(__DIR__ . '/config_local.php')) {
            $databases = include(__DIR__ . '/config_local.php');
        } else {
            $databases = include(__DIR__ . '/config.php');
        }

        new ConnectionManager(['databases' => $databases]);

        $this->database = $databases[$this->driverName];
        $pdo_database = 'pdo_' . $this->driverName;

        if (!extension_loaded('pdo') || !extension_loaded($pdo_database)) {
            $this->markTestSkipped('pdo and ' . $pdo_database . ' extension are required.');
        }
    }

    protected function tearDown()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * @param bool $reset whether to clean up the test database
     * @param bool $open whether to open and populate test database
     * @return \Mindy\Query\Connection
     */
    public function getConnection($reset = true, $open = true)
    {
        if (!$reset && $this->db) {
            return $this->db;
        }
        $config = $this->database;
        if (isset($config['fixture'])) {
            $fixture = $config['fixture'];
            unset($config['fixture']);
        } else {
            $fixture = null;
        }
        try {
            $this->db = $this->prepareDatabase($config, $fixture, $open);
            ConnectionManager::setDb($this->driverName, $this->db);
        } catch (\Exception $e) {
            $this->markTestSkipped("Something wrong when preparing database: " . $e->getMessage());
        }
        return $this->db;
    }

    public function prepareDatabase($config, $fixture, $open = true)
    {
        if (!isset($config['class'])) {
            $config['class'] = 'Mindy\QueryBuilder\Connection';
        }
        /* @var $db \Mindy\Query\Connection */
        $db = Creator::createObject($config);
        if (!$open) {
            return $db;
        }
        $db->open();
        if ($fixture !== null) {
            $lines = explode(';', file_get_contents($fixture));
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    if ($db->pdo->exec($line) === false) {
                        var_dump($db->pdo->errorInfo());
                        die(1);
                    }
                }
            }
        }
        return $db;
    }

    /**
     * Returns a test configuration param from /data/config.php
     * @param  string $name params name
     * @return mixed  the value of the configuration param
     */
    public static function getParam($name)
    {
        if (static::$params === null) {
            static::$params = require(__DIR__ . '/config.php');
        }
        return static::$params[$name];
    }

    protected function getAdapter()
    {
        switch ($this->driverName) {
            case "sqlite":
                return new \Mindy\QueryBuilder\Sqlite\Adapter;
            case "pgsql":
                return new \Mindy\QueryBuilder\Pgsql\Adapter;
            case "mysql":
                return new \Mindy\QueryBuilder\Mysql\Adapter;
            default:
                throw new Exception('Unknown driver');
        }
    }

    protected function getQueryBuilder()
    {
        $adapter = $this->getAdapter();
        $lookupBuilder = new LegacyLookupBuilder();
        return new QueryBuilder($adapter, $lookupBuilder);
    }
}
