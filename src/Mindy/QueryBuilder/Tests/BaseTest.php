<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Mindy\QueryBuilder\QueryBuilder;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $driver = 'sqlite';
    /**
     * @var Connection
     */
    protected $connection;

    protected function setUp()
    {
        parent::setUp();
        $config = require __DIR__.'/config/'.(@getenv('TRAVIS') ? 'config_travis.php' : 'config.php');
        $driverConfig = [];
        if (isset($config[$this->driver]) && extension_loaded('pdo_'.$this->driver)) {
            $driverConfig = $config[$this->driver];
        } else {
            $this->markTestSkipped('Missing config for '.$this->driver.' driver');
        }

        $fixtures = $driverConfig['fixture'];
        unset($driverConfig['fixture']);

        $this->connection = DriverManager::getConnection($driverConfig);

        $this->loadFixtures($this->connection, $fixtures);
    }

    protected function getConnection()
    {
        return $this->connection;
    }

    protected function loadFixtures(Connection $connection, $fixtures)
    {
        $sql = file_get_contents($fixtures);
        if (empty($sql)) {
            return;
        }

        /* @var \PDOStatement $stmt */
        if ($connection instanceof \Doctrine\DBAL\Driver\PDOConnection) {
            // PDO Drivers
            try {
                $lines = 0;
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                do {
                    // Required due to "MySQL has gone away!" issue
                    $stmt->fetch();
                    $stmt->closeCursor();
                    ++$lines;
                } while ($stmt->nextRowset());
            } catch (\PDOException $e) {
                throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        } else {
            // Non-PDO Drivers (ie. OCI8 driver)
            $stmt = $connection->prepare($sql);
            $rs = $stmt->execute();
            if (!$rs) {
                $error = $stmt->errorInfo();
                throw new \RuntimeException($error[2], $error[0]);
            }
            $stmt->closeCursor();
        }
    }

    /**
     * @return \Mindy\QueryBuilder\BaseAdapter
     */
    protected function getAdapter()
    {
        return $this->getQueryBuilder()->getAdapter();
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return QueryBuilder::getInstance($this->getConnection());
    }

    /**
     * @param $sql
     *
     * @return string
     */
    protected function quoteSql($sql)
    {
        return $this->getAdapter()->quoteSql($sql);
    }

    protected function assertSql($sql, $actual)
    {
        $this->assertEquals($this->quoteSql($sql), trim($actual));
    }
}
