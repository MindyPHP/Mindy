<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 15:35
 */

namespace Mindy\QueryBuilder\LookupBuilder;

use Closure;
use Exception;
use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;
use Mindy\QueryBuilder\QueryBuilder;

abstract class Base implements ILookupBuilder
{
    /**
     * @var string
     */
    protected $default = 'exact';
    /**
     * @var string
     */
    protected $separator = '__';
    /**
     * @var callable|null
     */
    protected $callback = null;
    /**
     * @var callable|null
     */
    protected $joinCallback = null;
    /**
     * @var null|\Closure
     */
    protected $fetchColumnCallback = null;
    /**
     * @var ILookupCollection[]
     */
    private $_lookupCollections = [];

    /**
     * @param ILookupCollection $lookupCollection
     * @return $this
     */
    public function addLookupCollection(ILookupCollection $lookupCollection)
    {
        $this->_lookupCollections[] = $lookupCollection;
        return $this;
    }

    /**
     * @param Closure $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param Closure $callback
     * @return $this
     */
    public function setJoinCallback($callback)
    {
        $this->joinCallback = $callback;
        return $this;
    }
    
    public function setFetchColumnCallback(Closure $callback)
    {
        $this->fetchColumnCallback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getJoinCallback()
    {
        return $this->joinCallback;
    }

    public function fetchColumnName($column)
    {
        if ($this->fetchColumnCallback instanceof \Closure) {
            return $this->fetchColumnCallback->__invoke($column);
        } else {
            return $column;
        }
    }

    public function runCallback(QueryBuilder $queryBuilder, $lookupNodes, $value)
    {
        if ($this->callback instanceof Closure) {
            return $this->callback->__invoke($queryBuilder, $this, $lookupNodes, $value);
        } else {
            return null;
        }
    }

    public function runJoinCallback(QueryBuilder $queryBuilder, $lookupNodes)
    {
        if ($this->joinCallback instanceof Closure) {
            return $this->joinCallback->__invoke($queryBuilder, $this, $lookupNodes);
        } else {
            return null;
        }
    }

    public function getSeparator()
    {
        return $this->separator;
    }

    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param $lookup
     * @return bool
     */
    public function hasLookup($lookup)
    {
        foreach ($this->_lookupCollections as $collection) {
            if ($collection->has($lookup)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param $lookup
     * @param $column
     * @param $value
     * @return string
     * @throws Exception
     * @exception \Exception
     */
    public function runLookup(IAdapter $adapter, $lookup, $column, $value)
    {
        foreach ($this->_lookupCollections as $collection) {
            if ($collection->has($lookup)) {
                return $collection->process($adapter, $lookup, $column, $value);
            }
        }
        throw new Exception('Unknown lookup: ' . $lookup . ', column: ' . $column . ', value: ' . (is_array($value) ? print_r($value, true) : $value));
    }

    abstract public function parse(array $where);
}