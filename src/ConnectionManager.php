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
    protected $defaultConnection = 'default';

    /**
     * @var array|\Doctrine\DBAL\Connection[]
     */
    protected $connections = [];
    /**
     * @var null
     */
    protected $configuration = null;
    /**
     * @var null
     */
    protected $eventManager = null;

    /**
     * ConnectionManager constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @param array $connections
     */
    protected function setConnections(array $connections)
    {
        foreach ($connections as $name => $config) {
            $this->connections[$name] = DriverManager::getConnection($config, $this->configuration, $this->eventManager);
        }
    }

    /**
     * @param array $config
     */
    protected function configure(array $config)
    {
        foreach ($config as $key => $value) {
            if (method_exists($this, 'set' . ucfirst($key))) {
                $this->{'set' . ucfirst($key)}($value);
            } else {
                $this->{$key} = $value;
            }
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