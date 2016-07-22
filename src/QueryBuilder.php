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

class QueryBuilder
{
    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
    const TYPE_DROP_TABLE = 'DROP_TABLE';
    const TYPE_RAW = 'RAW';

    protected $update = [];
    protected $insert = [];
    protected $type = null;
    protected $alias = '';
    protected $select = ['*'];
    protected $from = '';
    protected $raw = '';
    protected $limit = '';
    protected $offset = '';
    protected $order = [];
    protected $group = [];
    protected $having;
    protected $union;
    protected $checkIntegrity = [];
    /**
     * @var array
     */
    protected $createTable = [];
    /**
     * @var string
     */
    protected $dropTable;
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

    protected $schema;

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
    public function setTypeInsert()
    {
        $this->type = self::TYPE_INSERT;
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
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return $this
     */
    public function select($select)
    {
        $this->select = $select;
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
        if (($where instanceof Q) == false) {
            $where = new QAnd($where);
        }
        $where->setLookupBuilder($this->getLookupBuilder());
        $where->setAdapter($this->getAdapter());
        $this->where = $where;
        return $this;
    }

    /**
     * @param $where
     * @return $this
     */
    public function addWhere($where)
    {
        if (empty($this->where)) {
            $this->where($where);
        } else {
            if (($where instanceof Q) == false) {
                $where = new QAnd($where);
            }
            $this->where->addWhere($where);
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
        $this->insert = [$tableName, $columns, $rows];
        return $this;
    }

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

            case self::TYPE_INSERT:
                list($tableName, $columns, $rows) = $this->insert;
                return $adapter->generateInsertSQL($tableName, $columns, $rows);

            case self::TYPE_UPDATE:
                list($tableName, $update) = $this->update;
                return $adapter->generateUpdateSQL($tableName, $update, $this->where);

            case self::TYPE_DELETE:
                return $adapter->generateDeleteSQL($this->from, $this->where);

            case self::TYPE_SELECT:
            default:
                // Fetch where conditions before pass it to adapter.
                // Reason: Dynamic sql build in callbacks
                $where = $adapter->sqlWhere($this->where);
                // $select, $from, $where, $order, $group, $limit, $offset, $join, $having, $union
                return $adapter->generateSelectSQL(
                    $this->select,
                    $this->from,
                    $where,
                    $this->order,
                    $this->group,
                    $this->limit,
                    $this->offset,
                    $this->generateJoin(),
                    $this->having,
                    $this->union
                );
        }
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function dropTable($tableName)
    {
        return $this->getAdapter()->sqlDropTable($tableName);
    }

    /**
     * @param $tableName
     * @param $columns
     * @param null $options
     * @return string
     */
    public function createTable($tableName, $columns, $options = null)
    {
        return $this->getAdapter()->sqlCreateTable($tableName, $columns, $options);
    }

    /**
     * @param $tableName
     * @param $columns
     * @param null $options
     * @return string
     */
    public function createTableIfNotExists($tableName, $columns, $options = null)
    {
        return $this->getAdapter()->sqlCreateTableIfNotExists($tableName, $columns, $options);
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
     * @param $columns
     * @return string
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        return $this->getAdapter()->sqlAddPrimaryKey($name, $table, $columns);
    }

    /**
     * @param $name
     * @param $table
     * @return string
     */
    public function dropPrimaryKey($name, $table)
    {
        return $this->getAdapter()->dropPrimaryKey($name, $table);
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
     * @param $column
     * @param $type
     * @return string
     */
    public function addColumn($tableName, $column, $type)
    {
        return $this->getAdapter()->sqlAddColumn($tableName, $column, $type);
    }
}
