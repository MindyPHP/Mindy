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
use Mindy\QueryBuilder\Q\Q;
use Mindy\QueryBuilder\Q\QAnd;

class QueryBuilder
{
    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
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
    /**
     * @var array|Q
     */
    protected $where;
    /**
     * @var array|Q
     */
    protected $exclude;
    protected $join = [];
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
     * QueryBuilder constructor.
     * @param BaseAdapter $adapter
     */
    public function __construct(BaseAdapter $adapter, ILookupBuilder $lookupBuilder)
    {
        $this->adapter = $adapter;
        
        $lookupBuilder->setQueryBuilder($this);
        $this->lookupBuilder = $lookupBuilder;
    }

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
    public function setSelect($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param $tableName string
     * @return $this
     */
    public function setFrom($tableName)
    {
        $this->from = $tableName;
        return $this;
    }

    /**
     * @param array $where lookups
     * @return $this
     */
    public function setWhere($where)
    {
        if (($where instanceof Q) == false) {
            $where = new QAnd($where);
        }
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
            $this->setWhere($where);
        } else {
            if (($where instanceof Q) == false) {
                $where = new QAnd($where);
            }
            $this->where->addWhere($where);
        }
        return $this;
    }

    /**
     * @param array $exclude lookups
     * @return $this
     */
    public function setExclude($exclude)
    {
        if (($exclude instanceof Q) == false) {
            $exclude = new QAnd($exclude);
        }
        $this->exclude = $exclude;
        return $this;
    }

    /**
     * @param $exclude
     * @return $this
     */
    public function addExclude($exclude)
    {
        if (empty($this->exclude)) {
            $this->setExclude($exclude);
        } else {
            if (($exclude instanceof Q) == false) {
                $exclude = new QAnd($exclude);
            }
            $this->exclude->addWhere($exclude);
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

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    protected function generateLimitSQL()
    {
        if (empty($this->limit)) {
            return '';
        }

        if ($this->hasLimit($this->limit)) {
            return ' LIMIT ' . $this->limit;
        }

        return '';
    }

    /**
     * Checks to see if the given offset is effective.
     * @param mixed $offset the given offset
     * @return boolean whether the offset is effective
     */
    protected function hasOffset($offset)
    {
        return is_integer($offset) && $offset > 0 || is_string($offset) && ctype_digit($offset) && $offset !== '0';
    }

    /**
     * @param $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return string
     */
    protected function generateOffsetSQL()
    {
        if (empty($this->offset)) {
            return '';
        }

        if ($this->hasOffset($this->offset)) {
            return ' OFFSET ' . $this->offset;
        }

        return '';
    }

    /**
     * Checks to see if the given limit is effective.
     * @param mixed $limit the given limit
     * @return boolean whether the limit is effective
     */
    protected function hasLimit($limit)
    {
        return is_string($limit) && ctype_digit($limit) || is_integer($limit) && $limit >= 0;
    }

    /**
     * Generate SELECT SQL
     * @return string
     */
    protected function generateSelectSQL()
    {
        $rawSelect = (array)$this->select;

        $adapter = $this->getAdapter();
        $alias = $this->getAlias();
        $select = [];
        foreach ($rawSelect as $column => $subQuery) {
            if (is_numeric($column)) {
                $column = $subQuery;
                $subQuery = '';
            }

            if (empty($subQuery) === false && strpos($subQuery, 'SELECT') !== false) {
                $value = '(' . $subQuery . ') AS ' . $adapter->quoteColumn(empty($alias) ? $column : $alias . '.' . $column);
            } else if (empty($subQuery) === false) {
                $value = $adapter->quoteColumn(empty($alias) ? $column : $alias . '.' . $column) . ' AS ' . $subQuery;
            } else if (empty($subQuery) && strpos($column, '.') !== false) {
                $newSelect = [];
                foreach (explode(',', $column) as $item) {
                    /*
                    if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $item, $matches)) {
                        list(, $rawColumn, $rawAlias) = $matches;
                    }
                    */
                    if (strpos($item, 'AS') !== false) {
                        list($rawColumn, $rawAlias) = explode('AS', $item);
                    } else {
                        $rawColumn = $item;
                        $rawAlias = '';
                    }

                    $newSelect[] = empty($rawAlias) ? $adapter->quoteColumn(trim($rawColumn)) : $adapter->quoteColumn(trim($rawColumn)) . ' AS ' . $adapter->quoteColumn(trim($rawAlias));
                }
                $value = implode(',', $newSelect);
            } else if (empty($subQuery) === false) {
                $value = $adapter->quoteColumn(empty($alias) ? $column : $alias . '.' . $column) . ' AS ' . $subQuery;
            } else {
                $value = $adapter->quoteColumn(empty($alias) ? $column : $alias . '.' . $column);
            }
            $select[] = $value;
        }
        return 'SELECT ' . implode(',', $select);
    }

    /**
     * Genertate FROM SQL
     * @return string
     */
    protected function generateFromSQL()
    {
        $alias = $this->getAlias();
        $adapter = $this->getAdapter();
        if (strpos($this->from, 'SELECT') !== false) {
            return ' FROM (' . $this->from . ')' . (empty($this->alias) ? '' : ' AS ' . $adapter->quoteTableName($alias));
        } else {
            $tableName = $adapter->getRawTableName($this->tablePrefix, $this->from);
            return ' FROM ' . $adapter->quoteTableName($tableName) . (empty($this->alias) ? '' : ' AS ' . $adapter->quoteTableName($alias));
        }
    }

    protected function getLookupBuilder()
    {
        return $this->lookupBuilder;
    }

    /**
     * @return BaseAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Generate WHERE SQL
     * @return string
     * @throws Exception
     */
    protected function generateWhereSQL()
    {
        if (empty($this->where) && empty($this->exclude)) {
            return '';
        }

        $adapter = $this->getAdapter();
        $lookupBuilder = $this->getLookupBuilder();
        if (empty($this->where)) {
            $whereSql = '';
        } else {
            $where = $this->where;
            $where->setLookupBuilder($lookupBuilder);
            $where->setAdapter($adapter);
            $whereSql = $where->toSQL();
        }

        if (empty($this->exclude)) {
            $excludeSql = '';
        } else {
            $exclude = $this->exclude;
            $exclude->setLookupBuilder($lookupBuilder);
            $exclude->setAdapter($adapter);
            $excludeSql = $exclude->toSQL();
        }

        if (empty($whereSql) && empty($excludeSql)) {
            return '';
        } else {
            $sql = ' WHERE ';
            if (!empty($whereSql)) {
                $sql .= $whereSql;
            }

            if (!empty($excludeSql)) {
                $sql .= ' AND NOT (' . $excludeSql . ')';
            }
            return $sql;
        }
    }

    /**
     * @param $joinType string LEFT JOIN, RIGHT JOIN, etc...
     * @param $tableName string
     * @param array $on link columns
     * @param string $alias string
     * @return $this
     * @throws Exception
     */
    public function setJoin($joinType, $tableName, array $on, $alias = '')
    {
        if (empty($alias)) {
            $this->join[] = [$joinType, $tableName, $on, $alias];
        } else {
            if (array_key_exists($alias, $this->join)) {
                throw new Exception('Alias already defined in $join');
            }
            $this->join[$alias] = [$joinType, $tableName, $on, $alias];
        }
        return $this;
    }

    /**
     * @param $alias string table alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string table alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string join sql
     * @throws Exception
     */
    protected function generateJoinSQL()
    {
        if (empty($this->join)) {
            return '';
        }

        $join = [];
        foreach ($this->join as $alias => $joinParams) {
            list($joinType, $tableName, $on, $alias) = $joinParams;

            $onSQL = [];
            $adapter = $this->getAdapter();
            foreach ($on as $leftColumn => $rightColumn) {
                $onSQL[] = $adapter->quoteColumn($leftColumn) . '=' . $adapter->quoteColumn($rightColumn);
            }

            if (strpos($tableName, 'SELECT') !== false) {
                $join[] = $joinType . ' (' . $adapter->quoteSql($this->tablePrefix, $tableName) . ')' . (empty($alias) ? '' : ' AS ' . $adapter->quoteColumn($alias)) . ' ON ' . implode(',', $onSQL);
            } else {
                $join[] = $joinType . ' ' . $adapter->quoteTableName($tableName) . (empty($alias) ? '' : ' AS ' . $adapter->quoteColumn($alias)) . ' ON ' . implode(',', $onSQL);
            }
        }

        return ' ' . implode(' ', $join);
    }

    /**
     * @param array $columns columns
     * @return $this
     */
    public function setGroup(array $columns)
    {
        $this->group = $columns;
        return $this;
    }

    /**
     * Generate GROUP SQL
     * @return string
     */
    protected function generateGroupSQL()
    {
        if (empty($this->group)) {
            return '';
        }

        $alias = $this->getAlias();
        $adapter = $this->getAdapter();
        $group = [];
        foreach ($this->group as $column) {
            $group[] = $adapter->quoteColumn(empty($alias) ? $column : $alias . '.' . $column);
        }

        return ' GROUP BY ' . implode(' ', $group);
    }

    /**
     * @param array|string $columns columns
     * @return $this
     */
    public function setOrder($columns)
    {
        $this->order = (array)$columns;
        return $this;
    }

    /**
     * Generate ORDER SQL
     * @return string
     */
    protected function generateOrderSQL()
    {
        if (empty($this->order)) {
            return '';
        }

        $order = [];
        $adapter = $this->getAdapter();
        $alias = $this->getAlias();
        foreach ($this->order as $column) {
            if (strpos($column, '-', 0) === 0) {
                $column = substr($column, 0, 1);
                $direction = 'DESC';
            } else {
                $direction = 'ASC';
            }

            $order[] = $adapter->quoteColumn(empty($alias) ? $column : $alias . '.' . $column) . ' ' . $direction;
        }

        return ' ORDER BY ' . implode(' ', $order);
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
     * Generate INSERT SQL
     * @return string
     */
    protected function generateInsertSQL()
    {
        return 'INSERT INTO ' . $this->getAdapter()->quoteTableName($this->from);
    }

    /**
     * @param array $values rows with columns [name => value...]
     * @return $this
     */
    public function setInsert(array $rows)
    {
        $this->insert = $rows;
        return $this;
    }

    /**
     * Generate INSERT VALUES SQL
     * @return string
     */
    protected function generateInsertValuesSQL()
    {
        $row = [];
        $adapter = $this->getAdapter();
        $columns = [];
        foreach ($this->insert as $entry) {
            if (empty($columns)) {
                $columns = array_map(function ($column) use ($adapter) {
                    return $adapter->quoteColumn($column);
                }, array_keys($entry));
            }
            $values = array_map(function ($value) use ($adapter) {
                return $adapter->quoteValue($value);
            }, array_values($entry));
            $row[] = '(' . implode(',', $values) . ')';
        }
        return ' (' . implode(',', $columns) . ') VALUES ' . implode(',', $row);
    }

    /**
     * Generate DELETE SQL
     * @return string
     */
    protected function generateDeleteSQL()
    {
        return 'DELETE';
    }

    /**
     * @param array $values columns [name => value...]
     * @return $this
     */
    public function setUpdate(array $values)
    {
        $this->update = $values;
        return $this;
    }

    /**
     * Generate UPDATE SQL
     * @return string
     */
    protected function generateUpdateSQL()
    {
        $updateSQL = [];
        $adapter = $this->getAdapter();
        foreach ($this->update as $column => $value) {
            $updateSQL[] = $adapter->quoteColumn($column) . '=' . $adapter->quoteValue($value);
        }

        $alias = $this->getAlias();
        $tableName = empty($alias) ? $this->from : $alias . '.' . $this->from;
        return 'UPDATE ' . $adapter->quoteTableName($tableName) . ' SET ' . implode(' ', $updateSQL);
    }

    public function setRaw($sql)
    {
        $this->setTypeRaw();
        $this->sql = $sql;
        return $this;
    }

    protected function generateRawSql()
    {
        return $this->getAdapter()->quoteSql($this->tablePrefix, $this->raw);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function toSQL()
    {
        switch ($this->getType()) {
            case self::TYPE_RAW:
                return $this->generateRawSql();
            case self::TYPE_INSERT:
                return strtr('{insert}{values}', [
                    '{insert}' => $this->generateInsertSQL(),
                    '{values}' => $this->generateInsertValuesSQL(),
                ]);
            case self::TYPE_UPDATE:
                return strtr('{update}{where}{join}{order}{group}', [
                    '{update}' => $this->generateUpdateSQL(),
                    '{where}' => $this->generateWhereSQL(),
                    '{group}' => $this->generateGroupSQL(),
                    '{order}' => $this->generateOrderSQL(),
                    '{join}' => $this->generateJoinSQL()
                ]);
            case self::TYPE_DELETE:
                return strtr('{delete}{from}{where}{join}{order}{group}', [
                    '{delete}' => $this->generateDeleteSQL(),
                    '{from}' => $this->generateFromSQL(),
                    '{where}' => $this->generateWhereSQL(),
                    '{group}' => $this->generateGroupSQL(),
                    '{order}' => $this->generateOrderSQL(),
                    '{join}' => $this->generateJoinSQL()
                ]);
            case self::TYPE_SELECT:
            default:
                return strtr('{select}{from}{where}{join}{group}{order}{limit}{offset}', [
                    '{select}' => $this->generateSelectSQL(),
                    '{from}' => $this->generateFromSQL(),
                    '{where}' => $this->generateWhereSQL(),
                    '{group}' => $this->generateGroupSQL(),
                    '{order}' => $this->generateOrderSQL(),
                    '{join}' => $this->generateJoinSQL(),
                    '{limit}' => $this->generateLimitSQL(),
                    '{offset}' => $this->generateOffsetSQL()
                ]);
        }
    }
}
