<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:17
 */

namespace Mindy\QueryBuilder;

use Exception;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;
use Mindy\QueryBuilder\Interfaces\ISQLGenerator;
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
    private $_select = ['*'];
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
     * Counter of joined tables aliases
     * @var int
     */
    private $_aliasesCount = 0;

    /**
     * QueryBuilder constructor.
     * @param BaseAdapter $adapter
     */
    public function __construct(BaseAdapter $adapter, ILookupBuilder $lookupBuilder, $schema = null)
    {
        $this->adapter = $adapter;
        $this->schema = $schema;

        $lookupBuilder->setQueryBuilder($this);
        $this->lookupBuilder = $lookupBuilder;
    }

    /**
     * @param ILookupCollection $lookupCollection
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
     * If type is null return TYPE_SELECT
     * @return string
     */
    public function getType()
    {
        return empty($this->_type) ? self::TYPE_SELECT : $this->_type;
    }

    /**
     * @param $select array|string columns
     * @param $distinct array|string columns
     * @return $this
     */
    public function select($select, $distinct = null)
    {
        $this->_select = $select;
        $this->_distinct = $distinct;
        return $this;
    }

    /**
     * @param $tableName string
     * @return $this
     */
    public function from($tableName)
    {
        $this->_from = $tableName;
        return $this;
    }

    /**
     * @param $alias string join alias
     * @return bool
     */
    public function hasJoin($alias)
    {
        return array_key_exists($alias, $this->_join);
    }

    /**
     * @param int $page
     * @param int $pageSize
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
     * @return $this
     */
    public function offset($offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * @return ILookupBuilder|\Mindy\QueryBuilder\LookupBuilder\Legacy
     */
    public function getLookupBuilder()
    {
        return $this->lookupBuilder;
    }

    public function distinct($columns = null)
    {
        $this->distinct = $columns;
        return $this;
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
     * @param array $on link columns
     * @param string $alias string
     * @return $this
     * @throws Exception
     */
    public function join($joinType, $tableName = '', array $on = [], $alias = '')
    {
        if (empty($alias)) {
            $this->_join[] = [$joinType, $tableName, $on, $alias];
        } else if (array_key_exists($alias, $this->_join)) {
            throw new Exception('Alias already defined in $join');
        } else {
            $this->_join[$alias] = [$joinType, $tableName, $on, $alias];
        }
        return $this;
    }

    /**
     * @param $sql
     * @param string $alias
     * @return $this
     */
    public function joinRaw($sql)
    {
        $this->_join[] = $sql;
        return $this;
    }

    /**
     * @param array $columns columns
     * @return $this
     */
    public function group($columns)
    {
        $this->_group = $columns;
        return $this;
    }

    /**
     * @param array|string $columns columns
     * @param null $options
     * @return $this
     */
    public function order($columns, $options = null)
    {
        $this->_order = $columns;
        $this->_orderOptions = $options;
        return $this;
    }

    /**
     * Clear properties
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
     * @param array $columns
     * @param array $rows
     * @return $this
     */
    public function insert($tableName, array $columns, array $rows)
    {
        return $this->getAdapter()->generateInsertSQL($tableName, $columns, $rows);
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     * @param $refTable
     * @param $refColumns
     * @param null $delete
     * @param null $update
     * @return string
     */
    public function addForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        return $this->getAdapter()->sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     * @param bool $unique
     * @return string
     */
    public function createIndex($tableName, $name, $columns, $unique = false)
    {
        return $this->getAdapter()->sqlCreateIndex($tableName, $name, $columns, $unique);
    }

    /**
     * @param $tableName
     * @param $name
     * @return mixed
     */
    public function dropForeignKey($tableName, $name)
    {
        return $this->getAdapter()->sqlDropForeignKey($tableName, $name);
    }

    /**
     * @param $tableName
     * @return string
     */
    public function truncateTable($tableName)
    {
        return $this->getAdapter()->sqlTruncateTable($tableName);
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function dropIndex($tableName, $name)
    {
        return $this->getAdapter()->sqlDropIndex($tableName, $name);
    }

    /**
     * @param $check
     * @param string $schema
     * @param string $tableName
     * @return string
     */
    public function checkIntegrity($check, $schema = '', $tableName = '')
    {
        return $this->getAdapter()->sqlCheckIntegrity($check, $schema, $tableName);
    }

    /**
     * @param $tableName string
     * @param array $values columns [name => value...]
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
            return (string)$condition;
        } else if (empty($condition)) {
            return '';
        }

        if (isset($condition[0])) {
            $operator = strtoupper(array_shift($condition));
            return $this->buildAndCondition($operator, $condition, $params);
        } else {
            return $this->parseCondition($condition);
        }
    }

    /**
     * @param $condition
     * @return string
     */
    protected function parseCondition($condition)
    {
        $parts = [];
        if ($condition instanceof Q) {
            $condition->setLookupBuilder($this->getLookupBuilder());
            $condition->setAdapter($this->getAdapter());
            $parts[] = $condition->toSQL();
        } else if ($condition instanceof QueryBuilder) {
            $parts[] = $condition->toSQL();
        } else if (is_array($condition)) {
            $conditions = $this->lookupBuilder->parse($condition);
            foreach ($conditions as $key => $value) {
                list($lookup, $column, $lookupValue) = $value;
                $parts[] = $this->lookupBuilder->runLookup($this->getAdapter(), $lookup, $column, $lookupValue);
            }
        } else if (is_string($condition)) {
            $parts[] = $condition;
        } else if ($condition instanceof Expression) {
            $parts[] = $condition->toSQL();
        }

        if (count($parts) === 1) {
            return $parts[0];
        } else {
            return '(' . implode(') AND (', $parts) . ')';
        }
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
            return '(' . implode(') ' . $operator . ' (', $parts) . ')';
        } else {
            return '';
        }
    }

    /**
     * @param $condition
     * @return $this
     */
    public function where($condition)
    {
        $this->_whereAnd[] = $condition;
        return $this;
    }

    /**
     * @param $condition
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

    public function buildWhere()
    {
        $params = [];
        $sql = $this->buildCondition($this->buildWhereTree(), $params);
        return empty($sql) ? '' : ' WHERE ' . $sql;
    }

    private function generateSelectSql()
    {
        // Fetch where conditions before pass it to adapter.
        // Reason: Dynamic sql build in callbacks

        $where = $this->buildWhere();
        $order = $this->buildOrder();
        $union = $this->buildUnion();
        return strtr('{select}{from}{join}{where}{group}{having}{order}{limit_offset}{union}', [
            '{select}' => $this->buildSelect(),
            '{from}' => $this->buildFrom(),
            '{where}' => $where,
            '{group}' => $this->buildGroup(),
            '{order}' => empty($union) ? $order : '',
            '{having}' => $this->buildHaving(),
            '{join}' => $this->buildJoin(),
            '{limit_offset}' => $this->buildLimitOffset(),
            '{union}' => empty($union) ? '' : $union . $order
        ]);
    }

    public function generateDeleteSql()
    {
        return strtr('{delete}{from}{where}', [
            '{delete}' => 'DELETE',
            '{from}' => $this->buildFrom(),
            '{where}' => $this->buildWhere()
        ]);
    }

    public function generateUpdateSql()
    {
        list($tableName, $values) = $this->_update;
        return strtr('{update}{where}', [
            '{update}' => $this->getAdapter()->sqlUpdate($tableName, $values),
            '{where}' => $this->buildWhere(),
        ]);
    }
    
    /**
     * @return string
     * @throws Exception
     */
    public function toSQL()
    {
        $type = $this->getType();
        if ($type == self::TYPE_SELECT) {
            return $this->generateSelectSql();
        } else if ($type == self::TYPE_UPDATE) {
            return $this->generateUpdateSql();
        } else if ($type == self::TYPE_DELETE) {
            return $this->generateDeleteSql();
        }

        throw new Exception('Unknown query type');
    }

    public function buildHaving()
    {
        return $this->getAdapter()->sqlHaving($this->_having);
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
            $sql .= ' ' . $this->getAdapter()->sqlUnion($union, $all);
        }

        return empty($sql) ? '' : $sql;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param $tableName
     * @param bool $isExists
     * @return string
     */
    public function dropTable($tableName, $isExists = false)
    {
        return $this->getAdapter()->sqlDropTable($tableName, $isExists);
    }

    /**
     * @param $tableName
     * @param $columns
     * @param null $options
     * @param bool $ifNotExists
     * @return string
     */
    public function createTable($tableName, $columns, $options = null, $ifNotExists = false)
    {
        return $this->getAdapter()->sqlCreateTable($tableName, $columns, $options, $ifNotExists);
    }

    /**
     * @param array|string|Q $where lookups
     * @return $this
     */
    public function having($having)
    {
        if (($having instanceof Q) == false) {
            $having = new QAnd($having);
        }
        $having->setLookupBuilder($this->getLookupBuilder());
        $having->setAdapter($this->getAdapter());
        $this->having = $having;
        return $this;
    }

    public function union($union, $all = false)
    {
        $this->_union[] = [$union, $all];
        return $this;
    }

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     * @return mixed
     */
    public function renameColumn($tableName, $oldName, $newName)
    {
        return $this->getAdapter()->sqlRenameColumn($tableName, $oldName, $newName);
    }

    /**
     * @param $oldTableName
     * @param $newTableName
     * @return mixed
     */
    public function renameTable($oldTableName, $newTableName)
    {
        return $this->getAdapter()->sqlRenameTable($oldTableName, $newTableName);
    }

    /**
     * @param $name
     * @param $table
     * @return string
     */
    public function dropPrimaryKey($tableName, $name)
    {
        return $this->getAdapter()->sqlDropPrimaryKey($tableName, $name);
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
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
     * @return string
     */
    public function alterColumn($tableName, $column, $type)
    {
        return $this->getAdapter()->sqlAlterColumn($tableName, $column, $type);
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function resetSequence($tableName, $name)
    {
        return $this->getAdapter()->sqlResetSequence($tableName, $name);
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function dropColumn($tableName, $name)
    {
        return $this->getAdapter()->sqlDropColumn($tableName, $name);
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    public function addColumn($tableName, $column, $type)
    {
        return $this->getAdapter()->sqlAddColumn($tableName, $column, $type);
    }

    /**
     * Makes alias for joined table
     * @param $table
     * @param bool $increment
     * @return string
     */
    public function makeAliasKey($table, $increment = true)
    {
        if ($increment) {
            $this->_aliasesCount += 1;
        }
        return strtr('{table}_{count}', [
            '{table}' => $this->getAdapter()->getRawTableName($table),
            '{count}' => $this->_aliasesCount
        ]);
    }

    /**
     * @return string
     */
    public function buildSelect()
    {
        $columns = [];
        if (is_array($this->_select)) {
            $builder = $this->getLookupBuilder();
            foreach ($this->_select as $select) {
                $newSelect = $builder->buildJoin($select);
                if ($newSelect === false) {
                    $columns[] = $select;
                } else {
                    list($alias, $joinColumn) = $newSelect;
                    var_dump($alias, $joinColumn);
                    $columns[] = $alias . '.' . $joinColumn;
                }
            }
        } else {
            $columns = $this->_select;
        }
        return $this->getAdapter()->sqlSelect($columns, $this->_distinct);
    }

    /**
     * @return string
     */
    public function buildColumns()
    {
        return $this->getAdapter()->buildColumns($this->_select);
    }

    public function buildJoin()
    {
        $sql = '';
        foreach ($this->_join as $join) {
            if (count($join) === 1) {
                $sql .= $this->getAdapter()->quoteSql($join);
            } else {
                list($joinType, $tableName, $on, $alias) = $join;
                $sql .= ' ' . $this->getAdapter()->sqlJoin($joinType, $tableName, $on, $alias);
            }
        }
        return empty($sql) ? '' : $sql;
    }

    public function buildOrder()
    {
        $sql = $this->getAdapter()->sqlOrderBy($this->_order, $this->_orderOptions);
        return empty($sql) ? '' : ' ORDER BY ' . $sql;
    }

    public function buildGroup()
    {
        $sql = $this->getAdapter()->sqlGroupBy($this->_group);
        return empty($sql) ? '' : ' GROUP BY ' . $sql;
    }

    public function buildFrom()
    {
        if (!empty($this->_alias) && !is_array($this->_from)) {
            $from = [$this->_alias => $this->_from];
        } else {
            $from = $this->_from;
        }
        $sql = $this->getAdapter()->sqlFrom($from);
        return empty($sql) ? '' : ' FROM ' . $sql;
    }
}
