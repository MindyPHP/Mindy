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
use Mindy\QueryBuilder\Q\Q;
use Mindy\QueryBuilder\QueryBuilder;

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
    public function sqlDropTableIfExists($tableName)
    {
        return "DROP TABLE IF EXISTS " . $this->quoteTableName($tableName);
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
        return empty($sql) ? '' : ' ' . $sql;
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    public function sqlAddColumn($tableName, $column, $type)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' ADD ' . $this->quoteColumn($column) . ' ' . $type;
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
}