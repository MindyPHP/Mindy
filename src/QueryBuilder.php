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
use Mindy\QueryBuilder\Interfaces\ISQLGenerator;
use Mindy\QueryBuilder\Q\Q;
use Mindy\QueryBuilder\Q\QAnd;
use Mindy\QueryBuilder\Q\QOr;

class QueryBuilder
{
    const TYPE_SELECT = 'SELECT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
    const TYPE_RAW = 'RAW';

    protected $update = [];
    protected $insert = [];
    protected $type = null;
    protected $alias = '';
    protected $select = ['*'];
    protected $from = [];
    protected $raw = '';
    protected $limit = '';
    protected $offset = '';
    protected $order = [];
    protected $group = [];
    protected $having;
    protected $union;
    protected $checkIntegrity = [];
    /**
     * @var array|Q
     */
    protected $where;
    public $join = [];
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
     * @var null|string|array
     */
    protected $distinct = null;
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

    /*
    public function __clone()
    {
        foreach ($this as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $this->$key;
            }
        }
    }
    */

    /**
     * @return $this
     */
    public function setTypeSelect()
    {
        $this->type = self::TYPE_SELECT;
        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeUpdate()
    {
        $this->type = self::TYPE_UPDATE;
        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDelete()
    {
        $this->type = self::TYPE_DELETE;
        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeRaw()
    {
        $this->type = self::TYPE_RAW;
        return $this;
    }

    /**
     * If type is null return TYPE_SELECT
     * @return string
     */
    public function getType()
    {
        return empty($this->type) ? self::TYPE_SELECT : $this->type;
    }

    /**
     * @param $select array|string columns
     * @param $distinct array|string columns
     * @return $this
     */
    public function select($select, $distinct = null)
    {
        $this->select = $select;
        $this->distinct = $distinct;
        return $this;
    }

    /**
     * @param $tableName string
     * @return $this
     */
    public function from($tableName)
    {
        $this->from = $tableName;
        return $this;
    }

    /**
     * @param array|string|Q $where lookups
     * @return $this
     */
    public function where($where)
    {
        if (empty($this->where)) {
            $this->where = ['AND', $where, null];
        } else {
            $this->where = ['AND', $this->where, $where];
        }
        return $this;
    }

    public function orWhere($where)
    {
        if (empty($this->where)) {
            $this->where = ['OR', $where, null];
        } else {
            $this->where = ['OR', $this->where, $where];
        }
        return $this;
    }

    /**
     * @param $where
     * @return $this
     */
    public function andWhere($where)
    {
        if (empty($this->where)) {
            $this->where = ['AND', $where, null];
        } else {
            $this->where = ['AND', $this->where, $where];
        }
        return $this;
    }

    /**
     * @param $alias string join alias
     * @return bool
     */
    public function hasJoin($alias)
    {
        return array_key_exists($alias, $this->join);
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return ILookupBuilder|\Mindy\QueryBuilder\LookupBuilder\Base
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
    public function join($joinType, $tableName, array $on, $alias = '')
    {
        if (empty($alias)) {
            $this->join[] = [$joinType, $tableName, $on, $alias];
        } else if (array_key_exists($alias, $this->join)) {
            throw new Exception('Alias already defined in $join');
        } else {
            $this->join[$alias] = [$joinType, $tableName, $on, $alias];
        }
        return $this;
    }

    /**
     * @param array $columns columns
     * @return $this
     */
    public function group(array $columns)
    {
        $this->group = $columns;
        return $this;
    }

    /**
     * @param array|string $columns columns
     * @param null $options
     * @return $this
     */
    public function order($columns, $options = null)
    {
        $this->order = [(array)$columns, $options];
        return $this;
    }

    /**
     * Clear properties
     * @return $this
     */
    public function clear()
    {
        $this->where = [];
        $this->join = [];
        $this->insert = [];
        $this->update = [];
        $this->group = [];
        $this->order = [];
        $this->select = [];
        $this->raw = '';
        $this->from = '';
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
        $this->update = [$tableName, $values];
        return $this;
    }

    public function raw($sql)
    {
        $this->setTypeRaw();
        $this->sql = $sql;
        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    private function generateJoin()
    {
        if (empty($this->join)) {
            return '';
        }

        $joinRaw = [];
        foreach ($this->join as $alias => $joinParams) {
            list($joinType, $tableName, $on, $alias) = $joinParams;
            $joinRaw[] = $this->getAdapter()->sqlJoin($joinType, $tableName, $on, $alias);
        }

        if (empty($joinRaw)) {
            $join = '';
        } else {
            $join = ' ' . implode(' ', $joinRaw);
        }
        return $join;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function toSQL()
    {
        $adapter = $this->getAdapter();

        switch ($this->getType()) {
            case self::TYPE_RAW:
                return $adapter->quoteSql($this->raw);

            case self::TYPE_UPDATE:
                list($tableName, $update) = $this->update;
                return $adapter->generateUpdateSQL($tableName, $update, $this->where);

            case self::TYPE_DELETE:
                return $adapter->generateDeleteSQL($this->from, $this->where);

            case self::TYPE_SELECT:
            default:
                // Fetch where conditions before pass it to adapter.
                // Reason: Dynamic sql build in callbacks

                $where = $this->where;
                if (($where instanceof Q) === false) {
                    $where = new QAnd($where);
                }
                $where->setAdapter($this->getAdapter());
                $where->setLookupBuilder($this->getLookupBuilder());

                // $select, $from, $where, $order, $group, $limit, $offset, $join, $having, $union, $distinct
                return $adapter->generateSelectSQL(
                    $this->select,
                    empty($this->alias) ? $this->from : [$this->alias => $this->from],
                    $adapter->sqlWhere($where),
                    $this->order,
                    $this->group,
                    $this->limit,
                    $this->offset,
                    $this->generateJoin(),
                    $this->having,
                    $this->union,
                    $this->distinct
                );
        }
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
        $this->union[] = [$union, $all];
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
}
