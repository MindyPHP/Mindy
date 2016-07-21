<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 11:08
 */

namespace Mindy\QueryBuilder\Pgsql;

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
     * @param string $tableName the name of the table whose primary key sequence will be reset
     * @param mixed $value the value for the primary key of the next new row inserted. If this is not set,
     * the next new row's primary key will have a value 1.
     * @return string the SQL statement for resetting sequence
     * @throws InvalidParamException if the table does not exist or there is no sequence associated with the table.
     */
    public function resetSequence($tableName, $value = null)
    {
        $table = $this->db->getTableSchema($tableName);
        if ($table !== null && $table->sequenceName !== null) {
            // c.f. http://www.postgresql.org/docs/8.1/static/functions-sequence.html
            $sequence = $this->db->quoteTableName($table->sequenceName);
            $tableName = $this->db->quoteTableName($tableName);
            if ($value === null) {
                $key = reset($table->primaryKey);
                $value = "(SELECT COALESCE(MAX(\"{$key}\"),0) FROM {$tableName})+1";
            } else {
                $value = (int)$value;
            }
            return "SELECT SETVAL('$sequence',$value,false)";
        } elseif ($table === null) {
            throw new InvalidParamException("Table not found: $tableName");
        } else {
            throw new InvalidParamException("There is not sequence associated with table '$tableName'.");
        }
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
    public function alterColumn($table, $column, $type)
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
        return 'RAND()';
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

    /**
     * @param $tableName
     * @return string
     */
    public function sqlDropTableIfExists($tableName)
    {
        // TODO: Implement sqlDropTableIfExists() method.
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
     * @return string
     */
    public function sqlTruncateTable($tableName)
    {
        // TODO: Implement sqlTruncateTable() method.
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
        // TODO: Implement sqlDropForeignKey() method.
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
        // TODO: Implement sqlDropPrimaryKey() method.
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
     * @param $tableName
     * @param $sequenceName
     * @return string
     */
    public function sqlResetSequence($tableName, $sequenceName)
    {
        // TODO: Implement sqlResetSequence() method.
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
}