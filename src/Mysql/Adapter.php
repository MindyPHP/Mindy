<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 13:06
 */

namespace Mindy\QueryBuilder\Mysql;

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
     * @return array
     */
    public function getLookupCollection()
    {
        return new LookupCollection($this->lookups);
    }

    public function getRandomOrder()
    {
        return 'RANDOM()';
    }

    /**
     * Builds a SQL statement for renaming a column.
     * @param string $table the table whose column is to be renamed. The name will be properly quoted by the method.
     * @param string $oldName the old name of the column. The name will be properly quoted by the method.
     * @param string $newName the new name of the column. The name will be properly quoted by the method.
     * @return string the SQL statement for renaming a DB column.
     * @throws Exception
     */
    public function renameColumn($table, $oldName, $newName)
    {
        $quotedTable = $this->quoteTableName($table);
        $row = $this->db->createCommand('SHOW CREATE TABLE ' . $quotedTable)->queryOne();
        if ($row === false) {
            throw new Exception("Unable to find column '$oldName' in table '$table'.");
        }
        if (isset($row['Create Table'])) {
            $sql = $row['Create Table'];
        } else {
            $row = array_values($row);
            $sql = $row[1];
        }
        if (preg_match_all('/^\s*`(.*?)`\s+(.*?),?$/m', $sql, $matches)) {
            foreach ($matches[1] as $i => $c) {
                if ($c === $oldName) {
                    return "ALTER TABLE $quotedTable CHANGE "
                    . $this->quoteColumn($oldName) . ' '
                    . $this->quoteColumn($newName) . ' '
                    . $matches[2][$i];
                }
            }
        }
        // try to give back a SQL anyway
        return "ALTER TABLE $quotedTable CHANGE "
        . $this->quoteColumn($oldName) . ' '
        . $this->quoteColumn($newName);
    }

    /**
     * @param $oldTableName
     * @param $newTableName
     * @return string
     */
    public function sqlRenameTable($oldTableName, $newTableName)
    {
        // TODO: Implement sqlRenameTable() method.
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
        return "TRUNCATE TABLE " . $this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function sqlDropIndex($tableName, $name)
    {
        // TODO: Implement sqlDropIndex() method.
    }

    /**
     * @param $tableName
     * @param $column
     * @return string
     */
    public function sqlDropColumn($tableName, $column)
    {
        // TODO: Implement sqlDropColumn() method.
    }

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     * @return mixed
     */
    public function sqlRenameColumn($tableName, $oldName, $newName)
    {
        // TODO: Implement sqlRenameColumn() method.
    }

    /**
     * @param $tableName
     * @param $name
     * @return mixed
     */
    public function sqlDropForeignKey($tableName, $name)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' DROP FOREIGN KEY ' . $this->quoteColumn($name);
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
    public function sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        // TODO: Implement sqlAddForeignKey() method.
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     * @return string
     */
    public function sqlAddPrimaryKey($tableName, $name, $columns)
    {
        // TODO: Implement sqlAddPrimaryKey() method.
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function sqlDropPrimaryKey($tableName, $name)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' DROP PRIMARY KEY';
    }

    /**
     * @param $value
     * @return string
     */
    public function getBoolean($value = null)
    {
        return (bool)$value ? 1 : 0;
    }

    protected function formatDateTime($value, $format)
    {
        if ($value === null) {
            $value = date($format);
        } elseif (is_numeric($value)) {
            $value = date($format, $value);
        } elseif (is_string($value)) {
            $value = date($format, strtotime($value));
        }
        return $value;
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
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    public function sqlAddColumn($tableName, $column, $type)
    {
        // TODO: Implement sqlAddColumn() method.
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
        // TODO: Implement sqlCreateIndex() method.
    }

    /**
     * @param array $columns
     * @return string
     */
    public function sqlDistinct(array $columns)
    {
        return 'DISTINCT ';
    }

    /**
     * @param $tableName
     * @param $sequenceName
     * @return string
     */
    public function sqlResetSequence($tableName, $sequenceName)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' AUTO_INCREMENT=' . $this->quoteColumn($sequenceName);
    }

    /**
     * @param bool $check
     * @param string $schema
     * @param string $table
     * @return string
     */
    public function sqlCheckIntegrity($check = true, $schema = '', $table = '')
    {
        return 'SET FOREIGN_KEY_CHECKS = ' . $this->getBoolean($check);
    }

    public function sqlLimitOffset($limit = null, $offset = null)
    {
        $sql = '';
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET ' . $offset;
            }
        } elseif ($this->hasOffset($offset)) {
            // limit is not optional in MySQL
            // http://stackoverflow.com/a/271650/1106908
            // http://dev.mysql.com/doc/refman/5.0/en/select.html#idm47619502796240
            $sql = "LIMIT $offset, 18446744073709551615"; // 2^64-1
        }

        return empty($sql) ? '' : ' ' . $sql;
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
            // limit is not optional in MySQL
            // http://stackoverflow.com/a/271650/1106908
            // http://dev.mysql.com/doc/refman/5.0/en/select.html#idm47619502796240
            $sql = "LIMIT $offset, 18446744073709551615"; // 2^64-1
        }
        return $sql;
    }
}