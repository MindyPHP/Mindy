<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 17:17
 */

namespace Mindy\QueryBuilder\Sqlite;

use Exception;
use Mindy\QueryBuilder\BaseAdapter;
use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\Interfaces\ISQLGenerator;

class Adapter extends BaseAdapter implements IAdapter, ISQLGenerator
{
    /**
     * Quotes a table name for use in a query.
     * A simple table name has no schema prefix.
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, "`") !== false ? $name : "`" . $name . "`";
    }

    /**
     * Quotes a column name for use in a query.
     * A simple column name has no prefix.
     * @param string $name column name
     * @return string the properly quoted column name
     */
    public function quoteSimpleColumnName($name)
    {
        return strpos($name, '`') !== false || $name === '*' ? $name : '`' . $name . '`';
    }

    /**
     * @return ILookupCollection
     */
    public function getLookupCollection()
    {
        return new LookupCollection($this->lookups);
    }

    /**
     * @return string
     */
    public function getRandomOrder()
    {
        return 'RANDOM()';
    }

    /**
     * @param $tableName
     * @param array $columns
     * @param null $options
     * @return string
     */
    public function sqlCreateTable($tableName, array $columns, $options = null)
    {
        $cols = [];
        foreach ($columns as $name => $type) {
            if (is_string($name)) {
                $cols[] = "\t" . $this->quoteColumn($name) . ' ' . $this->getColumnType($type);
            } else {
                $cols[] = "\t" . $type;
            }
        }
        $sql = "CREATE TABLE " . $this->quoteTableName($tableName) . " (\n" . implode(",\n", $cols) . "\n)";
        return $options === null ? $sql : $sql . ' ' . $options;
    }

    /**
     * @param $oldTableName
     * @param $newTableName
     * @return string
     */
    public function sqlRenameTable($oldTableName, $newTableName)
    {
        return 'RENAME TABLE ' . $this->quoteTableName($oldTableName) . ' TO ' . $this->quoteTableName($newTableName);
    }

    /**
     * @param $tableName
     * @return string
     */
    public function sqlDropTable($tableName)
    {
        return "DROP TABLE " . $this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @return string
     */
    public function sqlTruncateTable($tableName)
    {
        return "DELETE FROM " . $this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function sqlDropIndex($tableName, $name)
    {
        return 'DROP INDEX ' . $this->quoteTableName($name);
    }

    /**
     * @param $tableName
     * @param $column
     * @return string
     * @throws Exception
     */
    public function sqlDropColumn($tableName, $column)
    {
        throw new Exception(__METHOD__ . ' is not supported by SQLite.');
    }

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     * @return string
     * @throws Exception
     */
    public function sqlRenameColumn($tableName, $oldName, $newName)
    {
        throw new Exception(__METHOD__ . ' is not supported by SQLite.');
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     * @throws Exception
     */
    public function sqlDropForeignKey($tableName, $name)
    {
        throw new Exception(__METHOD__ . ' is not supported by SQLite.');
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
     * @throws Exception
     */
    public function sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        throw new Exception(__METHOD__ . ' is not supported by SQLite.');
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     * @throws Exception
     */
    public function sqlAlterColumn($tableName, $column, $type)
    {
        throw new Exception('Alter column is not supported by SQLite.');
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     * @return string
     * @throws Exception
     */
    public function sqlAddPrimaryKey($tableName, $name, $columns)
    {
        throw new Exception('Add primary key is not supported by SQLite.');
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     * @throws Exception
     */
    public function sqlDropPrimaryKey($tableName, $name)
    {
        throw new Exception('Drop primary key is not supported by SQLite.');
    }

    /**
     * @param $value
     * @return string
     */
    public function getBoolean($value = null)
    {
        return (bool)$value ? 1 : 0;
    }

    /**
     * @param $value string|\DateTime
     * @param $format string
     * @return string
     */
    protected function formatDateTime($value, $format)
    {
        if ($value === null) {
            $value = date($format);
        } elseif (is_numeric($value)) {
            $value = date($format, $value);
        } elseif (is_string($value)) {
            $value = date($format, strtotime($value));
        }
        return (string)$value;
    }

    /**
     * @param null $value
     * @return string
     */
    public function getDateTime($value = null)
    {
        return $this->formatDateTime($value, "Y-m-d H:i:s");
    }

    /**
     * @param null $value
     * @return string
     */
    public function getDate($value = null)
    {
        return $this->formatDateTime($value, "Y-m-d");
    }

    /**
     * @param null $value
     * @return mixed
     */
    public function getTimestamp($value = null)
    {
        return $value instanceof \DateTime ? $value->getTimestamp() : strtotime($value);
    }

    /**
     * @param $limit
     * @param null $offset
     * @return mixed
     */
    public function sqlLimitOffset($limit = null, $offset = null)
    {
        $sql = '';
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET ' . $offset;
            }
        } elseif ($this->hasOffset($offset)) {
            // limit is not optional in SQLite
            // http://www.sqlite.org/syntaxdiagrams.html#select-stmt
            $sql = 'LIMIT 9223372036854775807 OFFSET ' . $offset; // 2^63-1
        }
        return $sql;
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    public function sqlAddColumn($tableName, $column, $type)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' ADD ' . $this->quoteColumn($column) . ' ' . $this->getColumnType($type);
    }

    /**
     * @param $tableName
     * @param $name
     * @param array $columns
     * @param bool $unique
     * @return string
     */
    public function sqlCreateIndex($tableName, $name, array $columns, $unique = false)
    {
        return ($unique ? 'CREATE UNIQUE INDEX ' : 'CREATE INDEX ')
        . $this->quoteTableName($name) . ' ON '
        . $this->quoteTableName($tableName)
        . ' (' . $this->buildColumns($columns) . ')';
    }

    /**
     * @param array $columns
     * @return string
     */
    public function sqlDistinct(array $columns)
    {
        $quotedColumns = [];
        foreach ($columns as $column) {
            $quotedColumns[] = $this->quoteColumn($column);
        }
        return 'DISTINCT ' . implode(', ', $quotedColumns);
    }

    /**
     * @param $tables
     * @return string
     */
    public function sqlFrom($tables)
    {
        if (empty($tables)) {
            return '';
        }

        if (!is_array($tables)) {
            $tables = (array)$tables;
        }
        $quotedTableNames = [];
        foreach ($tables as $table) {
            $quotedTableNames[] = $this->quoteTableName($table);
        }
        return 'FROM ' . implode(', ', $quotedTableNames);
    }

    /**
     * @param $joinType string
     * @param $tableName string
     * @param $on string|array
     * @param $alias string
     * @return string
     */
    public function sqlJoin($joinType, $tableName, $on, $alias)
    {
        $onSQL = [];
        foreach ($on as $leftColumn => $rightColumn) {
            $onSQL[] = $this->quoteColumn($leftColumn) . '=' . $this->quoteColumn($rightColumn);
        }

        if (strpos($tableName, 'SELECT') !== false) {
            return $joinType . ' (' . $this->quoteSql($this->tablePrefix, $tableName) . ')' . (empty($alias) ? '' : ' AS ' . $this->quoteColumn($alias)) . ' ON ' . implode(',', $onSQL);
        } else {
            return $joinType . ' ' . $this->quoteTableName($tableName) . (empty($alias) ? '' : ' AS ' . $this->quoteColumn($alias)) . ' ON ' . implode(',', $onSQL);
        }
    }

    /**
     * @param $where string|array
     * @return string
     */
    public function sqlWhere($where)
    {
        // TODO: Implement sqlWhere() method.
    }

    /**
     * @param $having
     * @return string
     */
    public function sqlHaving($having)
    {
        // TODO: Implement sqlHaving() method.
    }

    /**
     * @param $unions
     * @return string
     */
    public function sqlUnion($unions)
    {
        $result = '';
        foreach ($unions as $i => $union) {
            $result .= 'UNION ' . ($union['all'] ? 'ALL ' : '') . '( ' . $unions[$i]['query'] . ' ) ';
        }
        return trim($result);
    }

    /**
     * @param $tableName
     * @param $sequenceName
     * @return string
     */
    public function sqlResetSequence($tableName, $sequenceName)
    {
        return 'UPDATE sqlite_sequence SET seq=' . $this->quoteValue($sequenceName) . ' WHERE name=' . $this->quoteTableName($tableName);
    }

    /**
     * @param bool $check
     * @param string $schema
     * @param string $table
     * @return string
     */
    public function sqlCheckIntegrity($check = true, $schema = '', $table = '')
    {
        return 'PRAGMA foreign_keys=' . $this->getBoolean($check);
    }

    /**
     * @param $columns
     * @return string
     */
    public function sqlGroupBy($columns)
    {
        $group = [];
        foreach ($columns as $column) {
            $group[] = $this->quoteColumn($column);
        }

        return 'GROUP BY ' . implode(' ', $group);
    }

    /**
     * @param $columns
     * @return string
     */
    public function sqlOrderBy($columns)
    {
        $order = [];
        foreach ($columns as $column) {
            if (strpos($column, '-', 0) === 0) {
                $column = substr($column, 0, 1);
                $direction = 'DESC';
            } else {
                $direction = 'ASC';
            }

            $order[] = $this->quoteColumn($column) . ' ' . $direction;
        }

        return 'ORDER BY ' . implode(' ', $order);
    }
}