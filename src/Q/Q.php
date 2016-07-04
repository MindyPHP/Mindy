<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 11:59
 */

namespace Mindy\QueryBuilder\Q;

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
        $sql = [];
        foreach ($this->where as $i => $where) {
            if (is_numeric($i)) {
                if (is_string($where)) {
                    $sql[] = $this->adapter->quoteSql($where);
                } else if (is_array($where)) {
                    $conditions = $this->lookupBuilder->parse($where);
                    $tmpSql = [];
                    foreach ($conditions as $condition) {
                        list($lookup, $column, $value) = $condition;
                        $tmpSql[] = $this->adapter->runLookup($lookup, $column, $value);
                    }
                    $sql[] = implode(' ' . $this->operator . ' ', $tmpSql);
                } else if ($where instanceof Q) {
                    $where->setLookupBuilder($this->lookupBuilder);
                    $where->setAdapter($this->adapter);
                    $sql[] = '(' . $where->toSQL() . ')';
                } else if ($where instanceof QueryBuilder) {
                    $sql[] = '(' . $where->toSQL() . ')';
                }
            } else {
                $conditions = $this->lookupBuilder->parse([
                    $i => $where
                ]);
                $tmpSql = [];
                foreach ($conditions as $condition) {
                    list($lookup, $column, $value) = $condition;
                    $tmpSql[] = $this->adapter->runLookup($lookup, $column, $value);
                }
                $sql[] = implode(' ' . $this->operator . ' ', $tmpSql);
            }
        }

        return implode(' ' . $this->operator . ' ', $sql);
    }
}