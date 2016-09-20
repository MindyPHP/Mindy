<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 15/09/16
 * Time: 14:20
 */

namespace Mindy\QueryBuilder;

use Doctrine\DBAL\DriverManager;

/**
 * Class ConnectionManager
 * @package Mindy\QueryBuilder
 */
class ConnectionManager
{
    /**
     * @var string
     */
    protected $defaultConnection;

    /**
     * @var array|\Doctrine\DBAL\Connection[]
     */
    protected $connections = [];

    /**
     * ConnectionManager constructor.
     * @param array $connections
     * @param $defaultConnection
     * @param null $configuration
     * @param null $eventManager
     */
    public function __construct(array $connections, $defaultConnection = 'default', $configuration = null, $eventManager = null)
    {
        $this->defaultConnection = $defaultConnection;
        foreach ($connections as $name => $config) {
            $this->connections[$name] = DriverManager::getConnection($config, $configuration, $eventManager);
        }
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setDefaultConnection(string $name)
    {
        $this->defaultConnection = $name;
        return $this;
    }

    /**
     * @param null $name
     * @return \Doctrine\DBAL\Connection|null
     */
    public function getConnection($name = null)
    {
        if (empty($name)) {
            $name = $this->defaultConnection;
        }
        return $this->connections[$name];
    }
}