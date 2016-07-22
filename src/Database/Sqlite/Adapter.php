<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 17:17
 */

namespace Mindy\QueryBuilder\Database\Sqlite;

use Exception;
use Mindy\QueryBuilder\BaseAdapter;
use Mindy\QueryBuilder\Exception\NotSupportedException;
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
        return 'ALTER TABLE ' . $this->quoteTableName($oldTableName) . ' RENAME TO ' . $this->quoteTableName($newTableName);
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
        return 'DROP INDEX ' . $this->quoteTableName($tableName) . '.' . $this->quoteColumn($name);
    }

    /**
     * @param $tableName
     * @param $column
     * @return string
     * @throws Exception
     */
    public function sqlDropColumn($tableName, $column)
    {
        throw new NotSupportedException('not supported by SQLite');
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
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     * @throws Exception
     */
    public function sqlDropForeignKey($tableName, $name)
    {
        throw new NotSupportedException('not supported by SQLite');
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
        throw new NotSupportedException('not supported by SQLite');
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
        throw new NotSupportedException('not supported by SQLite');
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
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     * @throws Exception
     */
    public function sqlDropPrimaryKey($tableName, $name)
    {
        throw new NotSupportedException('not supported by SQLite');
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
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' ADD COLUMN ' . $this->quoteColumn($column) . ' ' . $type;
    }

    /**
     * @param $sequenceName
     * @param $value
     * @return string
     */
    public function sqlResetSequence($sequenceName, $value = null)
    {
        return 'UPDATE sqlite_sequence SET seq=' . $this->quoteValue($value) . ' WHERE name=' . $this->quoteValue($sequenceName);
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
     * Creates a SQL statement for resetting the sequence value of a table's primary key.
     * The sequence will be reset such that the primary key of the next new row inserted
     * will have the specified value or 1.
     * @param string $tableName the name of the table whose primary key sequence will be reset
     * @param mixed $value the value for the primary key of the next new row inserted. If this is not set,
     * the next new row's primary key will have a value 1.
     * @return string the SQL statement for resetting sequence
     * @throws InvalidParamException if the table does not exist or there is no sequence associated with the table.
     */
    public function resetSequence($tableName, $value = null)
    {
        $db = $this->db;
        $table = $db->getTableSchema($tableName);
        if ($table !== null && $table->sequenceName !== null) {
            if ($value === null) {
                $key = reset($table->primaryKey);
                $tableName = $db->quoteTableName($tableName);
                $value = $this->db->useMaster(function (Connection $db) use ($key, $tableName) {
                    return $db->createCommand("SELECT MAX('$key') FROM $tableName")->queryScalar();
                });
            } else {
                $value = (int)$value - 1;
            }
            try {
                $db->createCommand("UPDATE sqlite_sequence SET seq='$value' WHERE name='{$table->name}'")->execute();
            } catch (Exception $e) {
                // it's possible that sqlite_sequence does not exist
            }
        } elseif ($table === null) {
            throw new InvalidParamException("Table not found: $tableName");
        } else {
            throw new InvalidParamException("There is not sequence associated with table '$tableName'.'");
        }
    }

    /**
     * @inheritdoc
     */
    public function buildLimit($limit, $offset)
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
            $sql = "LIMIT 9223372036854775807 OFFSET $offset"; // 2^63-1
        }
        return $sql;
    }
}