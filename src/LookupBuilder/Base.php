<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 15:35
 */

namespace Mindy\QueryBuilder\LookupBuilder;

use Closure;
use Mindy\QueryBuilder\Interfaces\ICallback;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
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
     * @var array
     */
    protected $lookups = [];
    /**
     * @var callable|null
     */
    protected $callback = null;
    /**
     * @var null
     */
    protected $qb = null;
    /**
     * @var null|\Closure
     */
    protected $fetchColumnCallback = null;

    public function __construct(array $lookups = [], ICallback $callback = null)
    {
        $this->lookups = $lookups;
        $this->callback = $callback;
    }

    public function setLookups(array $lookups)
    {
        $this->lookups = $lookups;
        return $this;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
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

    protected function fetchColumnName($column)
    {
        if ($this->fetchColumnCallback instanceof \Closure) {
            return $this->fetchColumnCallback->__invoke($column);
        } else {
            return $column;
        }
    }

    public function runCallback($lookupNodes, $value)
    {
        if ($this->callback instanceof Closure) {
            return $this->callback->__invoke($this->qb, $this, $lookupNodes, $value);
        } else {
            return null;
        }
    }

    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
        return $this;
    }

    public function getSeparator()
    {
        return $this->separator;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function hasLookup($lookup)
    {
        return array_key_exists($lookup, $this->lookups);
    }

    abstract public function parse(array $where);
}