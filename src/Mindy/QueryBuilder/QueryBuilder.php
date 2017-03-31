<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder;

use Doctrine\DBAL\Connection;
use Exception;
use Mindy\QueryBuilder\Aggregation\Aggregation;
use Mindy\QueryBuilder\Database\Mysql\Adapter as MysqlAdapter;
use Mindy\QueryBuilder\Database\Pgsql\Adapter as PgsqlAdapter;
use Mindy\QueryBuilder\Database\Sqlite\Adapter as SqliteAdapter;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;
use Mindy\QueryBuilder\Interfaces\ISQLGenerator;
use Mindy\QueryBuilder\LookupBuilder\LookupBuilder;
use Mindy\QueryBuilder\Q\Q;
use Mindy\QueryBuilder\Q\QAnd;

class QueryBuilder
{
    const TYPE_SELECT = 'SELECT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';

    /**
     * @var array|Q|string
     */
    private $_whereAnd = [];
    /**
     * @var array|Q|string
     */
    private $_whereOr = [];
    /**
     * @var array|string
     */
    private $_join = [];
    /**
     * @var array|string
     */
    private $_order = [];
    /**
     * @var null|string
     */
    private $_orderOptions = null;
    /**
     * @var array
     */
    private $_group = [];
    /**
     * @var array|string|\Mindy\QueryBuilder\Aggregation\Aggregation
     */
    private $_select = [];
    /**
     * @var null|string|array
     */
    private $_distinct = null;
    /**
     * @var array|string|null
     */
    private $_from = null;
    /**
     * @var array
     */
    private $_union = [];
    /**
     * @var null|string|int
     */
    private $_limit = null;
    /**
     * @var null|string|int
     */
    private $_offset = null;
    /**
     * @var array
     */
    private $_having = [];
    /**
     * @var null|string
     */
    private $_alias = null;
    /**
     * @var null|string sql query type SELECT|UPDATE|DELETE
     */
    private $_type = null;
    /**
     * @var array
     */
    private $_update = [];

    protected $tablePrefix = '';
    /**
     * @var BaseAdapter
     */
    protected $adapter;
    /**
     * @var ILookupBuilder
     */
    protected $lookupBuilder;
    /**
     * @var null
     */
    protected $schema;
    /**
     * Counter of joined tables aliases.
     *
     * @var int
     */
    private $_aliasesCount = 0;

    private $_joinAlias = [];
    /**
     * @var Connection
     */
    protected $connection;

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    public function getDatabasePlatform()
    {
        return $this->getConnection()->getDatabasePlatform();
    }

    /**
     * @param Connection $connection
     *
     * @throws Exception
     *
     * @return QueryBuilder
     */
    public static function getInstance(Connection $connection)
    {
        $driver = $connection->getDriver();
        switch ($driver->getName()) {
            case 'pdo_mysql':
                $adapter = new MysqlAdapter($connection);
                break;
            case 'pdo_sqlite':
                $adapter = new SqliteAdapter($connection);
                break;
            case 'pdo_pgsql':
                $adapter = new PgsqlAdapter($connection);
                break;
            default:
                throw new Exception('Unknown driver');
        }
        $lookupBuilder = new LookupBuilder();
        $lookupBuilder->addLookupCollection($adapter->getLookupCollection());

        return new self($connection, $adapter, $lookupBuilder);
    }

    /**
     * QueryBuilder constructor.
     *
     * @param Connection     $connection
     * @param BaseAdapter    $adapter
     * @param ILookupBuilder $lookupBuilder
     */
    public function __construct(Connection $connection, BaseAdapter $adapter, ILookupBuilder $lookupBuilder)
    {
        $this->connection = $connection;
        $this->adapter = $adapter;
        $this->lookupBuilder = $lookupBuilder;
    }

    /**
     * @param ILookupCollection $lookupCollection
     *
     * @return $this
     */
    public function addLookupCollection(ILookupCollection $lookupCollection)
    {
        $this->lookupBuilder->addLookupCollection($lookupCollection);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeSelect()
    {
        $this->_type = self::TYPE_SELECT;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeUpdate()
    {
        $this->_type = self::TYPE_UPDATE;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDelete()
    {
        $this->_type = self::TYPE_DELETE;

        return $this;
    }

    /**
     * If type is null return TYPE_SELECT.
     *
     * @return string
     */
    public function getType()
    {
        return empty($this->_type) ? self::TYPE_SELECT : $this->_type;
    }

    public function distinct($distinct)
    {
        $this->_distinct = $distinct;

        return $this;
    }

    /**
     * @param Aggregation $aggregation
     * @param string      $columnAlias
     *
     * @return string
     */
    protected function buildSelectFromAggregation(Aggregation $aggregation)
    {
        $tableAlias = $this->getAlias();
        $rawColumns = $aggregation->getFields();
        $newSelect = $this->getLookupBuilder()->buildJoin($this, $rawColumns);
        if ($newSelect === false) {
            if (empty($tableAlias) || $rawColumns === '*') {
                $columns = $rawColumns;
            } else {
                $columns = $tableAlias.'.'.$rawColumns;
            }
        } else {
            list($alias, $joinColumn) = $newSelect;
            $columns = $alias.'.'.$joinColumn;
        }
        $fieldsSql = $this->getAdapter()->buildColumns($columns);
        $aggregation->setFieldsSql($fieldsSql);

        return $this->getAdapter()->quoteSql($aggregation->toSQL());
    }

    /**
     * @param $columns
     *
     * @return array|string
     */
    public function buildColumnsqwe($columns)
    {
        if (!is_array($columns)) {
            if ($columns instanceof Aggregation) {
                $columns->setFieldsSql($this->buildColumns($columns->getFields()));

                return $this->quoteSql($columns->toSQL());
            } elseif (strpos($columns, '(') !== false) {
                return $this->quoteSql($columns);
            }
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach ($columns as $i => $column) {
            if ($column instanceof Expression) {
                $columns[$i] = $this->quoteSql($column->toSQL());
            } elseif (strpos($column, 'AS') !== false) {
                if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $column, $matches)) {
                    list(, $rawColumn, $rawAlias) = $matches;
                    $columns[$i] = $this->quoteColumn($rawColumn).' AS '.$this->quoteColumn($rawAlias);
                }
            } elseif (strpos($column, '(') === false) {
                $columns[$i] = $this->quoteColumn($column);
            }
        }

        return is_array($columns) ? implode(', ', $columns) : $columns;
    }

    /**
     * @return string
     */
    public function buildSelect()
    {
        if (empty($this->_select)) {
            $this->_select = ['*'];
        }

        $builder = $this->getLookupBuilder();
        if (is_array($this->_select)) {
            $select = [];
            foreach ($this->_select as $alias => $column) {
                if ($column instanceof Aggregation) {
                    $select[$alias] = $this->buildSelectFromAggregation($column);
                } elseif (is_string($column)) {
                    if (strpos($column, 'SELECT') !== false) {
                        $select[$alias] = $column;
                    } else {
                        $select[$alias] = $this->addColumnAlias($builder->fetchColumnName($column));
                    }
                } else {
                    $select[$alias] = $column;
                }
            }
        } elseif (is_string($this->_select)) {
            $select = $this->addColumnAlias($this->_select);
        }

        return $this->getAdapter()->sqlSelect($select, $this->_distinct);
    }

    public function select($select, $distinct = null)
    {
        if ($distinct !== null) {
            $this->distinct($distinct);
        }

        if (empty($select)) {
            $this->_select = [];

            return $this;
        }

        $builder = $this->getLookupBuilder();
        $parts = [];
        if (is_array($select)) {
            foreach ($select as $key => $part) {
                if (is_string($part)) {
                    $newSelect = $builder->buildJoin($this, $part);
                    if ($newSelect) {
                        list($alias, $column) = $newSelect;
                        $parts[$key] = $alias.'.'.$column;
                    } else {
                        $parts[$key] = $part;
                    }
                } else {
                    $parts[$key] = $part;
                }
            }
        } elseif (is_string($select)) {
            $newSelect = $builder->buildJoin($this, $select);
            if ($newSelect) {
                list($alias, $column) = $newSelect;
                $parts[$alias] = $column;
            } else {
                $parts[] = $select;
            }
        } else {
            $parts[] = $select;
        }
        $this->_select = $parts;

        return $this;
    }

    /**
     * @param $select array|string columns
     * @param $distinct array|string columns
     *
     * @return $this
     */
    public function selectOld($select, $distinct = null)
    {
        if ($distinct !== null) {
            $this->distinct($distinct);
        }

        if (empty($select)) {
            $this->_select = [];

            return $this;
        }

        $tableAlias = $this->getAlias();
        $columns = [];
        $builder = $this->getLookupBuilder();
        if (is_array($select)) {
            foreach ($select as $columnAlias => $partSelect) {
                if ($partSelect instanceof Aggregation) {
                    $columns[$columnAlias] = $this->buildSelectFromAggregation($partSelect, $columnAlias);
                } elseif ($partSelect instanceof Expression) {
                    $columns[$columnAlias] = $this->getAdapter()->quoteSql($partSelect->toSQL());
                } elseif (strpos($partSelect, 'SELECT') !== false) {
                    if (empty($columnAlias)) {
                        $columns[$columnAlias] = '('.$partSelect.')';
                    } else {
                        $columns[$columnAlias] = '('.$partSelect.') AS '.$columnAlias;
                    }
                } else {
                    $newSelect = $builder->buildJoin($this, $partSelect);
                    if ($newSelect === false) {
                        $columns[$columnAlias] = empty($tableAlias) ? $partSelect : $tableAlias.'.'.$partSelect;
                        var_dump(empty($tableAlias) ? $partSelect : $tableAlias.'.'.$partSelect);
                    } else {
                        list($alias, $joinColumn) = $newSelect;
                        $columns[$columnAlias] = $alias.'.'.$joinColumn.' AS '.$partSelect;
                    }
                }
            }
        } elseif ($select instanceof Aggregation) {
            $columns = $this->buildSelectFromAggregation($select);
        } else {
            $columns = $select;
        }
        $this->_select = $this->getAdapter()->buildColumns($columns);

        return $this;
    }

    /**
     * @param $tableName string
     *
     * @return $this
     */
    public function from($tableName)
    {
        $this->_from = $tableName;

        return $this;
    }

    /**
     * @param $alias string join alias
     *
     * @return bool
     */
    public function hasJoin($alias)
    {
        return array_key_exists($alias, $this->_join);
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    public function paginate($page = 1, $pageSize = 10)
    {
        $this->limit($pageSize);
        $this->offset($page > 1 ? $pageSize * ($page - 1) : 0);

        return $this;
    }

    public function limit($limit)
    {
        $this->_limit = $limit;

        return $this;
    }

    /**
     * @param $offset
     *
     * @return $this
     */
    public function offset($offset)
    {
        $this->_offset = $offset;

        return $this;
    }

    /**
     * @return ILookupBuilder|\Mindy\QueryBuilder\LookupBuilder\LookupBuilder
     */
    public function getLookupBuilder()
    {
        return $this->lookupBuilder;
    }

    /**
     * @return BaseAdapter|ISQLGenerator
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param $joinType string LEFT JOIN, RIGHT JOIN, etc...
     * @param $tableName string
     * @param array  $on    link columns
     * @param string $alias string
     *
     * @throws Exception
     *
     * @return $this
     */
    public function join($joinType, $tableName = '', $on = [], $alias = '')
    {
        if (is_string($joinType) && empty($tableName)) {
            $this->_join[] = $this->getAdapter()->quoteSql($joinType);
        } elseif ($tableName instanceof self) {
            $this->_join[] = $this->getAdapter()->sqlJoin($joinType, $tableName, $on, $alias);
        } else {
            $this->_join[$tableName] = $this->getAdapter()->sqlJoin($joinType, $tableName, $on, $alias);
            $this->_joinAlias[$tableName] = $alias;
        }

        return $this;
    }

    /**
     * @param $sql
     * @param string $alias
     *
     * @return $this
     */
    public function joinRaw($sql)
    {
        $this->_join[] = $this->getAdapter()->quoteSql($sql);

        return $this;
    }

    /**
     * @param string|array $columns columns
     *
     * @return $this
     */
    public function group($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->_group = $columns;

        return $this;
    }

    /**
     * @param string|array $columns columns
     *
     * @return $this
     */
    public function addGroupBy($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->_group = array_merge($this->_group, $columns);

        return $this;
    }

    /**
     * @param array|string $columns columns
     * @param null         $options
     *
     * @return $this
     */
    public function order($columns, $options = null)
    {
        $this->_order = $columns;
        $this->_orderOptions = $options;

        return $this;
    }

    /**
     * Clear properties.
     *
     * @return $this
     */
    public function clear()
    {
        $this->_whereAnd = [];
        $this->_whereOr = [];
        $this->_join = [];
        $this->_insert = [];
        $this->_update = [];
        $this->_group = [];
        $this->_order = [];
        $this->_select = [];
        $this->_from = '';
        $this->_union = [];
        $this->_having = [];

        return $this;
    }

    /**
     * @param $tableName
     * @param array $rows
     *
     * @return $this
     */
    public function insert($tableName, $rows)
    {
        return $this->getAdapter()->generateInsertSQL($tableName, $rows);
    }

    /**
     * @param $tableName string
     * @param array $values columns [name => value...]
     *
     * @return $this
     */
    public function update($tableName, array $values)
    {
        $this->_update = [$tableName, $values];

        return $this;
    }

    public function raw($sql)
    {
        return $this->getAdapter()->quoteSql($sql);
    }

    public function getAlias()
    {
        return $this->_alias;
    }

    public function setAlias($alias)
    {
        $this->_alias = $alias;

        return $this;
    }

    public function buildCondition($condition, &$params = [])
    {
        if (!is_array($condition)) {
            return (string) $condition;
        } elseif (empty($condition)) {
            return '';
        }

        if (isset($condition[0]) && is_string($condition[0])) {
            $operatorRaw = array_shift($condition);
            $operator = strtoupper($operatorRaw);

            return $this->buildAndCondition($operator, $condition, $params);
        }

        return $this->parseCondition($condition);
    }

    public function getJoinAlias($tableName)
    {
        return $this->_joinAlias[$tableName];
    }

    /**
     * @param $condition
     *
     * @return string
     */
    protected function parseCondition($condition)
    {
        $tableAlias = $this->getAlias();
        $parts = [];
        if ($condition instanceof Expression) {
            $parts[] = $this->getAdapter()->quoteSql($condition->toSQL());
        } elseif ($condition instanceof Q) {
            $condition->setLookupBuilder($this->getLookupBuilder());
            $condition->setAdapter($this->getAdapter());
            $condition->setTableAlias($tableAlias);
            $parts[] = $condition->toSQL($this);
        } elseif ($condition instanceof self) {
            $parts[] = $condition->toSQL();
        } elseif (is_array($condition)) {
            foreach ($condition as $key => $value) {
                if ($value instanceof Q) {
                    $parts[] = $this->parseCondition($value);
                } else {
                    $value = $this->getAdapter()->prepareValue($value);

                    list($lookup, $column, $lookupValue) = $this->lookupBuilder->parseLookup($this, $key, $value);
                    $column = $this->getLookupBuilder()->fetchColumnName($column);
                    if (empty($tableAlias) === false && strpos($column, '.') === false) {
                        $column = $tableAlias.'.'.$column;
                    }
                    $parts[] = $this->lookupBuilder->runLookup($this->getAdapter(), $lookup, $column, $lookupValue);
                }
            }

            /*
            $conditions = $this->lookupBuilder->parse($condition);
            foreach ($conditions as $key => $value) {
                list($lookup, $column, $lookupValue) = $value;
                $column = $this->getLookupBuilder()->fetchColumnName($column);
                if (empty($tableAlias) === false) {
                    $column = $tableAlias . '.' . $column;
                }
                $parts[] = $this->lookupBuilder->runLookup($this->getAdapter(), $lookup, $column, $lookupValue);
            }
            */
        } elseif (is_string($condition)) {
            $parts[] = $condition;
        } elseif ($condition instanceof Expression) {
            $parts[] = $condition->toSQL();
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        return '('.implode(') AND (', $parts).')';
    }

    public function buildAndCondition($operator, $operands, &$params)
    {
        $parts = [];
        foreach ($operands as $operand) {
            if (is_array($operand)) {
                $operand = $this->buildCondition($operand, $params);
            } else {
                $operand = $this->parseCondition($operand);
            }
            if ($operand !== '') {
                $parts[] = $this->getAdapter()->quoteSql($operand);
            }
        }
        if (!empty($parts)) {
            return '('.implode(') '.$operator.' (', $parts).')';
        }

        return '';
    }

    /**
     * @param $condition
     *
     * @return $this
     */
    public function where($condition)
    {
        $this->_whereAnd[] = $condition;

        return $this;
    }

    /**
     * @param $condition
     *
     * @return $this
     */
    public function orWhere($condition)
    {
        $this->_whereOr[] = $condition;

        return $this;
    }

    /**
     * @return array
     */
    public function buildWhereTree()
    {
        $where = [];
        foreach ($this->_whereAnd as $condition) {
            if (empty($where)) {
                $where = ['and', $condition];
            } else {
                $where = ['and', $where, ['and', $condition]];
            }
        }

        foreach ($this->_whereOr as $condition) {
            if (empty($where)) {
                $where = ['or', $condition];
            } else {
                $where = ['or', $where, ['and', $condition]];
            }
        }

        return $where;
    }

    public function getSelect()
    {
        return $this->_select;
    }

    public function buildWhere()
    {
        $params = [];
        $sql = $this->buildCondition($this->buildWhereTree(), $params);

        return empty($sql) ? '' : ' WHERE '.$sql;
    }

//    protected function prepareJoin()
//    {
//        $builder = $this->getLookupBuilder();
//        if (is_array($this->_select)) {
//            foreach ($this->_select as $select) {
//                if (strpos($select, '__') > 0) {
//                    $builder->buildJoin($select);
//                }
//            }
//        } else {
//            if (strpos($this->_select, '__') > 0) {
//                $builder->buildJoin($this->_select);
//            }
//        }
//
//        foreach ($this->_order as $order) {
//            $builder->buildJoin($order);
//        }
//
//        foreach ($this->_group as $group) {
//            $builder->buildJoin($group);
//        }
//    }

    private function generateSelectSql()
    {
        // Fetch where conditions before pass it to adapter.
        // Reason: Dynamic sql build in callbacks

        // $this->prepareJoin();

        $where = $this->buildWhere();
        $order = $this->buildOrder();
        $union = $this->buildUnion();

        /*
        $hasAggregation = false;
        if (is_array($this->_select)) {
            foreach ($this->_select as $key => $value) {
                if ($value instanceof Aggregation) {

                }
            }
        } else {
            $hasAggregation = $this->_select instanceof Aggregation;
        }
        */

        $select = $this->buildSelect();
        $from = $this->buildFrom();
        $join = $this->buildJoin();
        $group = $this->buildGroup();
        $having = $this->buildHaving();
        $limitOffset = $this->buildLimitOffset();

        return strtr('{select}{from}{join}{where}{group}{having}{order}{limit_offset}{union}', [
            '{select}' => $select,
            '{from}' => $from,
            '{where}' => $where,
            '{group}' => $group,
            '{order}' => empty($union) ? $order : '',
            '{having}' => $having,
            '{join}' => $join,
            '{limit_offset}' => $limitOffset,
            '{union}' => empty($union) ? '' : $union.$order,
        ]);
    }

    public function generateDeleteSql()
    {
        return strtr('{delete}{from}{where}', [
            '{delete}' => 'DELETE',
            '{from}' => $this->buildFrom(),
            '{where}' => $this->buildWhere(),
        ]);
    }

    public function generateUpdateSql()
    {
        list($tableName, $values) = $this->_update;
        $this->setAlias(null);

        return strtr('{update}{where}', [
            '{update}' => $this->getAdapter()->sqlUpdate($tableName, $values),
            '{where}' => $this->buildWhere(),
        ]);
    }

    /**
     * @throws Exception
     *
     * @return string
     */
    public function toSQL()
    {
        $type = $this->getType();
        if ($type == self::TYPE_SELECT) {
            return $this->generateSelectSql();
        } elseif ($type == self::TYPE_UPDATE) {
            return $this->generateUpdateSql();
        } elseif ($type == self::TYPE_DELETE) {
            return $this->generateDeleteSql();
        }

        throw new Exception('Unknown query type');
    }

    public function buildHaving()
    {
        return $this->getAdapter()->sqlHaving($this->_having, $this);
    }

    public function buildLimitOffset()
    {
        return $this->getAdapter()->sqlLimitOffset($this->_limit, $this->_offset);
    }

    public function buildUnion()
    {
        $sql = '';
        foreach ($this->_union as $part) {
            list($union, $all) = $part;
            $sql .= ' '.$this->getAdapter()->sqlUnion($union, $all);
        }

        return empty($sql) ? '' : $sql;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param $tableName
     * @param $columns
     * @param null $options
     * @param bool $ifNotExists
     *
     * @return string
     */
    public function createTable($tableName, $columns, $options = null, $ifNotExists = false)
    {
        return $this->getAdapter()->sqlCreateTable($tableName, $columns, $options, $ifNotExists);
    }

    /**
     * @param array|string|Q $where lookups
     *
     * @return $this
     */
    public function having($having)
    {
        if (($having instanceof Q) == false) {
            $having = new QAnd($having);
        }
        $having->setLookupBuilder($this->getLookupBuilder());
        $having->setAdapter($this->getAdapter());
        $this->_having = $having;

        return $this;
    }

    public function union($union, $all = false)
    {
        $this->_union[] = [$union, $all];

        return $this;
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     *
     * @return string
     */
    public function addPrimaryKey($tableName, $name, $columns)
    {
        return $this->getAdapter()->sqlAddPrimaryKey($tableName, $name, $columns);
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     *
     * @return string
     */
    public function alterColumn($tableName, $column, $type)
    {
        return $this->getAdapter()->sqlAlterColumn($tableName, $column, $type);
    }

    /**
     * Makes alias for joined table.
     *
     * @param $table
     * @param bool $increment
     *
     * @return string
     */
    public function makeAliasKey($table, $increment = true)
    {
        //        if ($increment) {
//            $this->_aliasesCount += 1;
//        }
        return strtr('{table}_{count}', [
            '{table}' => $this->getAdapter()->getRawTableName($table),
            '{count}' => $this->_aliasesCount + 1,
        ]);
    }

    public function getJoin($tableName)
    {
        return $this->_join[$tableName];
    }

    /**
     * @param $column
     *
     * @return string
     */
    protected function addColumnAlias($column)
    {
        $tableAlias = $this->getAlias();
        if (empty($tableAlias)) {
            return $column;
        }

        if (strpos($column, '.') === false &&
            strpos($column, '(') === false &&
            strpos($column, 'SELECT') === false
        ) {
            return $tableAlias.'.'.$column;
        }

        return $column;
    }

    protected function applyTableAlias($column)
    {
        // If column already has alias - skip
        if (strpos($column, '.') === false) {
            $tableAlias = $this->getAlias();

            return empty($tableAlias) ? $column : $tableAlias.'.'.$column;
        }

        return $column;
    }

    public function buildJoin()
    {
        if (empty($this->_join)) {
            return '';
        }
        $join = [];
        foreach ($this->_join as $part) {
            $join[] = $part;
        }

        return ' '.implode(' ', $join);
    }

    /**
     * @param $order
     *
     * @return string
     */
    protected function buildOrderJoin($order)
    {
        if (strpos($order, '-', 0) === false) {
            $direction = 'ASC';
        } else {
            $direction = 'DESC';
            $order = substr($order, 1);
        }
        $order = $this->getLookupBuilder()->fetchColumnName($order);
        $newOrder = $this->getLookupBuilder()->buildJoin($this, $order);
        if ($newOrder === false) {
            return [$order, $direction];
        }
        list($alias, $column) = $newOrder;

        return [$alias.'.'.$column, $direction];
    }

    public function getOrder()
    {
        return [$this->_order, $this->_orderOptions];
    }

    public function buildOrder()
    {
        /*
         * не делать проверку по empty(), проваливается половина тестов с ORDER BY
         * и проваливается тест с построением JOIN по lookup
         */
        if ($this->_order === null) {
            return '';
        }

        $order = [];
        if (is_array($this->_order)) {
            foreach ($this->_order as $column) {
                if ($column === '?') {
                    $order[] = $this->getAdapter()->getRandomOrder();
                } else {
                    list($newColumn, $direction) = $this->buildOrderJoin($column);
                    $order[$this->applyTableAlias($newColumn)] = $direction;
                }
            }
        } elseif (is_string($this->_order)) {
            $columns = preg_split('/\s*,\s*/', $this->_order, -1, PREG_SPLIT_NO_EMPTY);
            $order = array_map(function ($column) {
                $temp = explode(' ', $column);
                if (count($temp) == 2) {
                    return $this->getAdapter()->quoteColumn($temp[0]).' '.$temp[1];
                }

                return $this->getAdapter()->quoteColumn($column);
            }, $columns);
            $order = implode(', ', $order);
        } else {
            $order = $this->buildOrderJoin($this->_order);
        }

        $sql = $this->getAdapter()->sqlOrderBy($order, $this->_orderOptions);

        return empty($sql) ? '' : ' ORDER BY '.$sql;
    }

    public function buildGroup()
    {
        $sql = $this->getAdapter()->sqlGroupBy($this->_group);

        return empty($sql) ? '' : ' GROUP BY '.$sql;
    }

    public function buildFrom()
    {
        if (!empty($this->_alias) && !is_array($this->_from)) {
            $from = [$this->_alias => $this->_from];
        } else {
            $from = $this->_from;
        }
        $sql = $this->getAdapter()->sqlFrom($from);

        return empty($sql) ? '' : ' FROM '.$sql;
    }
}
