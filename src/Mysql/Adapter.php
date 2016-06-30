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

class Adapter extends BaseAdapter implements IAdapter
{
    /**
     * @var array mapping from abstract column types (keys) to physical column types (values).
     */
    public $typeMap = [
        Schema::TYPE_PK => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
        Schema::TYPE_BIGPK => 'bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY',
        Schema::TYPE_STRING => 'varchar(255)',
        Schema::TYPE_TEXT => 'text',
        Schema::TYPE_SMALLINT => 'smallint(6)',
        Schema::TYPE_INTEGER => 'int(11)',
        Schema::TYPE_BIGINT => 'bigint(20)',
        Schema::TYPE_FLOAT => 'float',
        Schema::TYPE_DECIMAL => 'decimal(10,0)',
        Schema::TYPE_DATETIME => 'datetime',
        Schema::TYPE_TIMESTAMP => 'timestamp',
        Schema::TYPE_TIME => 'time',
        Schema::TYPE_DATE => 'date',
        Schema::TYPE_BINARY => 'blob',
        Schema::TYPE_BOOLEAN => 'tinyint(1)',
        Schema::TYPE_MONEY => 'decimal(19,4)',
    ];
    
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

    public function convertToDateTime($value = null)
    {
        static $dateTimeFormat = "Y-m-d H:i:s";
        if ($value === null) {
            $value = date($dateTimeFormat);
        } elseif (is_numeric($value)) {
            $value = date($dateTimeFormat, $value);
        } elseif (is_string($value)) {
            $value = date($dateTimeFormat, strtotime($value));
        }
        return $value;
    }

    public function convertToBoolean($value)
    {
        return (bool)$value ? 1 : 0;
    }

    /**
     * @inheritdoc
     */
    public function generateLimitOffsetSQL($limit, $offset)
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
     * Builds a SQL statement for dropping a foreign key constraint.
     * @param string $name the name of the foreign key constraint to be dropped. The name will be properly quoted by the method.
     * @param string $table the table whose foreign is to be dropped. The name will be properly quoted by the method.
     * @return string the SQL statement for dropping a foreign key constraint.
     */
    public function dropForeignKey($name, $table)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($table) . ' DROP FOREIGN KEY ' . $this->quoteColumn($name);
    }

    /**
     * Builds a SQL statement for removing a primary key constraint to an existing table.
     * @param string $name the name of the primary key constraint to be removed.
     * @param string $table the table that the primary key constraint will be removed from.
     * @return string the SQL statement for removing a primary key constraint from an existing table.
     */
    public function dropPrimaryKey($name, $table)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($table) . ' DROP PRIMARY KEY';
    }

    /**
     * Creates a SQL statement for resetting the sequence value of a table's primary key.
     * The sequence will be reset such that the primary key of the next new row inserted
     * will have the specified value or 1.
     * @param string $tableName the name of the table whose primary key sequence will be reset
     * @param mixed $value the value for the primary key of the next new row inserted. If this is not set,
     * the next new row's primary key will have a value 1.
     * @return string the SQL statement for resetting sequence
     * @throws Exception if the table does not exist or there is no sequence associated with the table.
     */
    public function resetSequence($tableName, $value = null)
    {
        $table = $this->getTableSchema($tableName);
        if ($table !== null && $table->sequenceName !== null) {
            $tableName = $this->quoteTableName($tableName);
            if ($value === null) {
                $key = reset($table->primaryKey);
                $value = $this->db->createCommand("SELECT MAX(`$key`) FROM $tableName")->queryScalar() + 1;
            } else {
                $value = (int) $value;
            }
            return "ALTER TABLE $tableName AUTO_INCREMENT=$value";
        } elseif ($table === null) {
            throw new Exception("Table not found: $tableName");
        } else {
            throw new Exception("There is no sequence associated with table '$tableName'.");
        }
    }

    /**
     * Builds a SQL statement for enabling or disabling integrity check.
     * @param boolean $check whether to turn on or off the integrity check.
     * @param string $table the table name. Meaningless for MySQL.
     * @param string $schema the schema of the tables. Meaningless for MySQL.
     * @return string the SQL statement for checking integrity
     */
    public function checkIntegrity($check = true, $schema = '', $table = '')
    {
        return 'SET FOREIGN_KEY_CHECKS = ' . ($check ? 1 : 0);
    }
}