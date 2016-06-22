<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:17
 */

namespace Mindy\QueryBuilder;

use Exception;

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
    protected $select = [];
    protected $from = '';
    protected $raw = '';
    protected $order = [];
    protected $group = [];
    protected $where = [];
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
    public function setWhere(array $where)
    {
        $this->where = $where;
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

    /**
     * Generate SELECT SQL
     * @return string
     */
    public function generateSelectSQL()
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
    public function generateFromSQL()
    {
        $alias = $this->getAlias();
        $adapter = $this->getAdapter();
        if (strpos($this->from, 'SELECT') !== false) {
            return ' FROM (' . $this->from . ')' . (empty($this->alias) ? '' : ' AS '. $adapter->quoteTableName($alias));
        } else {
            $tableName = $adapter->getRawTableName($this->tablePrefix, $this->from);
            return ' FROM ' . $adapter->quoteTableName($tableName) . (empty($this->alias) ? '' : ' AS '. $adapter->quoteTableName($alias));
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
    public function generateWhereSQL()
    {
        $conditions = $this
            ->getLookupBuilder()
            ->setWhere($this->where)
            ->generateCondition();
        $whereSQL = [];
        $adapter = $this->getAdapter();
        foreach ($conditions as $item) {
            list($lookup, $column, $value) = $item;
            $whereSQL[] = $adapter->runLookup($lookup, $column, $value);
        }

        return empty($conditions) ? '' : ' WHERE ' . implode(', ', $whereSQL);
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
    public function generateJoinSQL()
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
    public function generateGroupSQL()
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
     * @param array $columns columns
     * @return $this
     */
    public function setOrder(array $columns)
    {
        $this->order = $columns;
        return $this;
    }

    /**
     * Generate ORDER SQL
     * @return string
     */
    public function generateOrderSQL()
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
    public function generateInsertSQL()
    {
        return 'INSERT INTO ' . $this->getAdapter()->quoteTableName($this->from);
    }

    /**
     * @param array $values columns [name => value...]
     * @return $this
     */
    public function setInsert(array $values)
    {
        $this->insert = $values;
        return $this;
    }

    /**
     * Generate INSERT VALUES SQL
     * @return string
     */
    public function generateInsertValuesSQL()
    {
        $columns = [];
        $values = [];
        $adapter = $this->getAdapter();
        foreach ($this->insert as $column => $value) {
            $columns[] = $adapter->quoteColumn($column);
            $values[] = $adapter->quoteValue($value);
        }
        return ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ')';
    }

    /**
     * Generate DELETE SQL
     * @return string
     */
    public function generateDeleteSQL()
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
    public function generateUpdateSQL()
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

    public function generateRawSql()
    {
        return $this->getAdapter()->quoteSql($this->tablePrefix, $this->raw);
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
                return strtr('{select}{from}{where}{join}{group}{order}', [
                    '{select}' => $this->generateSelectSQL(),
                    '{from}' => $this->generateFromSQL(),
                    '{where}' => $this->generateWhereSQL(),
                    '{group}' => $this->generateGroupSQL(),
                    '{order}' => $this->generateOrderSQL(),
                    '{join}' => $this->generateJoinSQL()
                ]);
        }
    }
}
