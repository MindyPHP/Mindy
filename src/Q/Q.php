<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 11:59
 */

namespace Mindy\QueryBuilder\Q;

use Mindy\QueryBuilder\Expression;
use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
use Mindy\QueryBuilder\QueryBuilder;

abstract class Q
{
    /**
     * @var array|string|Q
     */
    protected $where;
    /**
     * @var string
     */
    protected $operator;
    /**
     * @var ILookupBuilder
     */
    protected $lookupBuilder;
    /**
     * @var IAdapter
     */
    protected $adapter;

    public function __construct($where)
    {
        $this->where = (array)$where;
    }

    public function setLookupBuilder(ILookupBuilder $lookupBuilder)
    {
        $this->lookupBuilder = $lookupBuilder;
        return $this;
    }

    public function setAdapter(IAdapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function addWhere($where)
    {
        $this->where[] = $where;
        return $this;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function toSQL()
    {
        return $this->parseWhere();
    }

    protected function parseWhere()
    {
        return $this->parseConditions($this->where);
    }

    /**
     * @param array $where
     * @return string
     */
    protected function parseConditions(array $where)
    {
        $sql = '';
        list($operator, $childWhere, $condition) = $where;
        if (is_array($childWhere) && count($childWhere) === 3) {
            $whereSql = $this->parseConditions($childWhere);
            $sql .= '(' . $whereSql . ') ' . strtoupper($operator) . ' (' . $this->parsePart($condition) . ')';
        } else {
            $sql .= $this->parsePart($childWhere);
        }
        return $sql;
    }

    protected function parsePart($part)
    {
        if (is_string($part)) {
            return $part;
        } else if (is_array($part)) {
            $conditions = $this->lookupBuilder->parse($part);
            $sql = [];
            foreach ($conditions as $condition) {
                list($lookup, $column, $value) = $condition;
                $sql[] = $this->adapter->runLookup($lookup, $column, $value);
            }
            return implode(' AND ', $sql);
        } else if ($part instanceof Q) {
            $part->setLookupBuilder($this->lookupBuilder);
            $part->setAdapter($this->adapter);
            return $part->toSQL();
        } else if ($part instanceof QueryBuilder) {
            return $part->toSQL();
        }
    }
}