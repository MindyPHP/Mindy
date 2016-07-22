<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 11:08
 */

namespace Mindy\QueryBuilder\Database\Pgsql;

use Exception;
use Mindy\QueryBuilder\BaseAdapter;
use Mindy\QueryBuilder\Interfaces\IAdapter;

class Adapter extends BaseAdapter implements IAdapter
{
    /**
     * Quotes a string value for use in a query.
     * Note that if the parameter is not a string, it will be returned without change.
     *
     * Note sqlite3:
     * A string constant is formed by enclosing the string in single quotes (').
     * A single quote within the string can be encoded by putting two single
     * quotes in a row - as in Pascal. C-style escapes using the backslash
     * character are not supported because they are not standard SQL.
     *
     * @param string $str string to be quoted
     * @return string the properly quoted string
     * @see http://www.php.net/manual/en/function.PDO-quote.php
     */
    public function quoteValue($str)
    {
        if ($str === true) {
            return 'TRUE';
        } else if ($str === false) {
            return 'FALSE';
        } else if ($str === null) {
            return 'NULL';
        } else {
            return parent::quoteValue($str);
        }
    }

    /**
     * Creates a SQL statement for resetting the sequence value of a table's primary key.
     * The sequence will be reset such that the primary key of the next new row inserted
     * will have the specified value or 1.
     * @param string $sequenceName the name of the table whose primary key sequence will be reset
     * @param mixed $value the value for the primary key of the next new row inserted. If this is not set,
     * the next new row's primary key will have a value 1.
     * @return string the SQL statement for resetting sequence
     * @throws Exception if the table does not exist or there is no sequence associated with the table.
     */
    public function sqlResetSequence($sequenceName, $value)
    {
        return "SELECT SETVAL('" . $sequenceName . "', " . $this->quoteValue($value) . ",false)";
    }

    /**
     * @param $limit
     * @param null $offset
     * @return mixed
     */
    public function sqlLimitOffset($limit = null, $offset = null)
    {
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET ' . $offset;
            }
            return ' ' . $sql;
        } elseif ($this->hasOffset($offset)) {
            // limit is not optional in SQLite
            // http://www.sqlite.org/syntaxdiagrams.html#select-stmt
            // If the LIMIT expression evaluates to a negative value, then there
            // is no upper bound on the number of rows returned.
            return ' LIMIT ALL OFFSET ' . $offset; // 2^63-1
        }

        return '';
    }
    
    /**
     * Builds a SQL statement for enabling or disabling integrity check.
     * @param boolean $check whether to turn on or off the integrity check.
     * @param string $schema the schema of the tables.
     * @param string $table the table name.
     * @return string the SQL statement for checking integrity
     */
    public function checkIntegrity($check = true, $schema = '', $table = '')
    {
        $enable = $check ? 'ENABLE' : 'DISABLE';
        $schema = $schema ? $schema : $this->db->getSchema()->defaultSchema;
        $tableNames = $table ? [$table] : $this->db->getSchema()->getTableNames($schema);
        $command = '';
        foreach ($tableNames as $tableName) {
            $tableName = '"' . $schema . '"."' . $tableName . '"';
            $command .= "ALTER TABLE $tableName $enable TRIGGER ALL; ";
        }
        // enable to have ability to alter several tables
        $this->db->getMasterPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        return $command;
    }

    /**
     * Builds a SQL statement for changing the definition of a column.
     * @param string $table the table whose column is to be changed. The table name will be properly quoted by the method.
     * @param string $column the name of the column to be changed. The name will be properly quoted by the method.
     * @param string $type the new column type. The [[getColumnType()]] method will be invoked to convert abstract
     * column type (if any) into the physical one. Anything that is not recognized as abstract type will be kept
     * in the generated SQL. For example, 'string' will be turned into 'varchar(255)', while 'string not null'
     * will become 'varchar(255) not null'. You can also use PostgreSQL-specific syntax such as `SET NOT NULL`.
     * @return string the SQL statement for changing the definition of a column.
     */
    public function sqlAlterColumn($table, $column, $type)
    {
        // https://github.com/yiisoft/yii2/issues/4492
        // http://www.postgresql.org/docs/9.1/static/sql-altertable.html
        if (!preg_match('/^(DROP|SET|RESET)\s+/i', $type)) {
            $type = 'TYPE ' . $type;
        }
        return 'ALTER TABLE ' . $this->quoteTableName($table) . ' ALTER COLUMN '
        . $this->quoteColumn($column) . ' ' . $type;
    }

    /**
     * @return LookupCollection
     */
    public function getLookupCollection()
    {
        return new LookupCollection($this->lookups);
    }

    public function getRandomOrder()
    {
        return 'RANDOM()';
    }

    public function convertToBoolean($value)
    {
        return (bool)$value ? 'TRUE' : 'FALSE';
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

    /**
     * Quotes a table name for use in a query.
     * A simple table name has no schema prefix.
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, '"') !== false ? $name : '"' . $name . '"';
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

    public function sqlDistinct(array $columns)
    {
        if (!empty($columns)) {
            $select = 'SELECT DISTINCT ';
            if (is_string($columns)) {
                return $select . $columns;
            } else if (is_array($columns)) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if (is_numeric($key)) {
                        $select .= $value;
                    } else {
                        $select .= 'ON (' . $key . ') ' . $value;
                    }
                    if (count($columns) != $i) {
                        $select .= ', ';
                    }
                    $i++;
                }
                return $select;
            }
        }
        return 'SELECT DISTINCT';
    }

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function sqlDropIndex($tableName, $name)
    {
        return 'DROP INDEX ' . $this->quoteColumn($name);
    }

    /**
     * @param string $tableName
     * @param string $name
     * @return string
     */
    public function sqlDropPrimaryKey($tableName, $name)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' DROP CONSTRAINT ' . $this->quoteColumn($name);
    }

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     * @return mixed
     */
    public function sqlRenameColumn($tableName, $oldName, $newName)
    {
        return "ALTER TABLE {$this->quoteTableName($tableName)} RENAME COLUMN " . $this->quoteColumn($oldName) . ' TO ' . $this->quoteColumn($newName);
    }

    /**
     * @param $value
     * @return string
     */
    public function getBoolean($value = null)
    {
        return (bool)$value ? 'TRUE' : 'FALSE';
    }

    /**
     * @param null $value
     * @return string
     */
    public function getDateTime($value = null)
    {
        // TODO: Implement getDateTime() method.
    }

    /**
     * @param null $value
     * @return string
     */
    public function getDate($value = null)
    {
        // TODO: Implement getDate() method.
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
     * @param bool $check
     * @param string $schema
     * @param string $table
     * @return string
     */
    public function sqlCheckIntegrity($check = true, $schema = '', $table = '')
    {
        // TODO: Implement sqlCheckIntegrity() method.
    }

    /**
     * @param $tableName
     * @param $name
     * @return mixed
     */
    public function sqlDropForeignKey($tableName, $name)
    {
        // TODO: Implement sqlDropForeignKey() method.
    }
}