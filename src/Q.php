<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 11:59
 */

namespace Mindy\QueryBuilder;

class Q
{
    /**
     * @var array
     */
    protected $where = [];
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

    public function __construct(array $where, $operator = 'AND')
    {
        $this->where = $where;
        $this->operator = $operator;
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
            if ($where instanceof Q) {
                $where->setLookupBuilder($this->lookupBuilder);
                $where->setAdapter($this->adapter);
                $sql[] = '(' . $where->toSQL() . ')';

            } else if (is_numeric($i)) {
                $conditions = $this->lookupBuilder->setWhere($where)->generateCondition();
                $tmpSql = [];
                foreach ($conditions as $condition) {
                    list($lookup, $column, $value) = $condition;
                    $tmpSql[] = $this->adapter->runLookup($lookup, $column, $value);
                }
                $sql[] = implode(' ' . $this->operator . ' ', $tmpSql);

            } else if (!is_numeric($i)) {
                $conditions = $this->lookupBuilder->setWhere([$i => $where])->generateCondition();
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