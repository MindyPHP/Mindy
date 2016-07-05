<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:50
 */

namespace Mindy\QueryBuilder;

use Mindy\QueryBuilder\Aggregation\Aggregation;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;
use Mindy\QueryBuilder\Interfaces\ISQLGenerator;
use Mindy\QueryBuilder\Q\Q;

abstract class BaseAdapter implements ISQLGenerator
{
    /**
     * @var string
     */
    protected $tablePrefix = '';
    /**
     * @var null|\PDO
     */
    protected $driver = null;
    /**
     * @var array of lookups Closure
     */
    protected $lookups = [];

    public function __construct($driver = null, array $lookups = [])
    {
        $this->driver = $driver;
        $this->lookups = $lookups;
    }

    /**
     * @return BaseLookupCollection|ILookupCollection
     */
    abstract public function getLookupCollection();

    /**
     * Quotes a column name for use in a query.
     * If the column name contains prefix, the prefix will also be properly quoted.
     * If the column name is already quoted or contains '(', '[[' or '{{',
     * then this method will do nothing.
     * @param string $name column name
     * @return string the properly quoted column name
     * @see quoteSimpleColumnName()
     */
    public function quoteColumn($name)
    {
        if (strpos($name, '(') !== false || strpos($name, '[[') !== false || strpos($name, '{{') !== false) {
            return $name;
        }
        if (($pos = strrpos($name, '.')) !== false) {
            $prefix = $this->quoteTableName(substr($name, 0, $pos)) . '.';
            $name = substr($name, $pos + 1);
        } else {
            $prefix = '';
        }
        return $prefix . $this->quoteSimpleColumnName($name);
    }

    /**
     * Quotes a simple column name for use in a query.
     * A simple column name should contain the column name only without any prefix.
     * If the column name is already quoted or is the asterisk character '*', this method will do nothing.
     * @param string $name column name
     * @return string the properly quoted column name
     */
    public function quoteSimpleColumnName($name)
    {
        return strpos($name, '"') !== false || $name === '*' ? $name : '"' . $name . '"';
    }

    /**
     * Returns the actual name of a given table name.
     * This method will strip off curly brackets from the given table name
     * and replace the percentage character '%' with [[Connection::tablePrefix]].
     * @param string $tablePrefix the table prefix
     * @param string $name the table name to be converted
     * @return string the real name of the given table name
     */
    public function getRawTableName($tablePrefix, $name)
    {
        if (strpos($name, '{{') !== false) {
            $name = preg_replace('/\\{\\{(.*?)\\}\\}/', '\1', $name);
            return str_replace('%', $tablePrefix, $name);
        } else {
            return $name;
        }
    }

    /**
     * @return null|\PDO
     */
    public function getDriver()
    {
        return $this->driver;
    }

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
        if (!is_string($str)) {
            return $str;
        }

        $driver = $this->getDriver();
        if ($driver && ($value = $driver->quote($str)) !== false) {
            return $value;
        } else {
            // the driver doesn't support quote (e.g. oci)
            return "'" . addcslashes(str_replace("'", "''", $str), "\000\n\r\\\032") . "'";
        }
    }

    /**
     * Quotes a table name for use in a query.
     * If the table name contains schema prefix, the prefix will also be properly quoted.
     * If the table name is already quoted or contains '(' or '{{',
     * then this method will do nothing.
     * @param string $name table name
     * @return string the properly quoted table name
     * @see quoteSimpleTableName()
     */
    public function quoteTableName($name)
    {
        if (strpos($name, '(') !== false || strpos($name, '{{') !== false) {
            return $name;
        }
        if (strpos($name, '.') === false) {
            return $this->quoteSimpleTableName($name);
        }
        $parts = explode('.', $name);
        foreach ($parts as $i => $part) {
            $parts[$i] = $this->quoteSimpleTableName($part);
        }
        return implode('.', $parts);
    }

    /**
     * Quotes a simple table name for use in a query.
     * A simple table name should contain the table name only without any schema prefix.
     * If the table name is already quoted, this method will do nothing.
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, "'") !== false ? $name : "'" . $name . "'";
    }

    public function quoteSql($sql)
    {
        $tablePrefix = $this->tablePrefix;
        return preg_replace_callback('/(\\{\\{(%?[\w\-\. ]+%?)\\}\\}|\\[\\[([\w\-\. ]+)\\]\\])|\\@([\w\-\. ]+)\\@/',
            function ($matches) use ($tablePrefix) {
                if (isset($matches[4])) {
                    return $this->quoteValue($this->convertToDbValue($matches[4]));
                } else if (isset($matches[3])) {
                    return $this->quoteColumn($matches[3]);
                } else {
                    return str_replace('%', $tablePrefix, $this->quoteTableName($matches[2]));
                }
            }, $sql);
    }

    public function convertToDbValue($rawValue)
    {
        $str = mb_strtolower((string)$rawValue, 'UTF-8');
        if ($str === 'true' || $str === 'false') {
            return $this->getBoolean($rawValue);
        } else if ($str === 'null') {
            return 'NULL';
        }
        return $rawValue;
    }

    /**
     * Checks to see if the given limit is effective.
     * @param mixed $limit the given limit
     * @return boolean whether the limit is effective
     */
    public function hasLimit($limit)
    {
        return is_string($limit) && ctype_digit($limit) || is_integer($limit) && $limit >= 0;
    }

    /**
     * Checks to see if the given offset is effective.
     * @param mixed $offset the given offset
     * @return boolean whether the offset is effective
     */
    public function hasOffset($offset)
    {
        return is_integer($offset) && $offset > 0 || is_string($offset) && ctype_digit($offset) && $offset !== '0';
    }

    /**
     * @param $lookup
     * @param $column
     * @param $value
     * @return string
     * @exception \Exception
     */
    public function runLookup($lookup, $column, $value)
    {
        return $this->getLookupCollection()->run($this, $lookup, $column, $value);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return string the LIMIT and OFFSET clauses
     */
    public function sqlLimitOffset($limit = null, $offset = null)
    {
        $sql = '';
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
        }
        if ($this->hasOffset($offset)) {
            $sql .= ' OFFSET ' . $offset;
        }

        if (empty($sql)) {
            return '';
        }

        return ' ' . ltrim($sql);
    }

    public function buildColumns($columns)
    {
        if (!is_array($columns)) {
            if (strpos($columns, '(') !== false) {
                return $columns;
            } else {
                $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        foreach ($columns as $i => $column) {
            if ($column instanceof Expression) {
                $columns[$i] = $column->expression;
            } elseif (strpos($column, '(') === false) {
                $columns[$i] = $this->quoteColumn($column);
            }
        }
        return is_array($columns) ? implode(', ', $columns) : $columns;
    }

    /**
     * Builds a SQL statement for adding a primary key constraint to an existing table.
     * @param string $name the name of the primary key constraint.
     * @param string $table the table that the primary key constraint will be added to.
     * @param string|array $columns comma separated string or array of columns that the primary key will consist of.
     * @return string the SQL statement for adding a primary key constraint to an existing table.
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        if (is_string($columns)) {
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach ($columns as $i => $col) {
            $columns[$i] = $this->quoteColumn($col);
        }
        return 'ALTER TABLE ' . $this->quoteTableName($table) . ' ADD CONSTRAINT '
        . $this->quoteColumn($name) . '  PRIMARY KEY ('
        . implode(', ', $columns) . ' )';
    }

    /**
     * Builds a SQL statement for removing a primary key constraint to an existing table.
     * @param string $name the name of the primary key constraint to be removed.
     * @param string $table the table that the primary key constraint will be removed from.
     * @return string the SQL statement for removing a primary key constraint from an existing table.
     */
    public function dropPrimaryKey($name, $table)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($table) . ' DROP CONSTRAINT ' . $this->quoteColumn($name);
    }

    public function sqlAlterColumn($tableName, $column, $type)
    {
        return 'ALTER TABLE ' . $this->quoteTableName($tableName) . ' CHANGE '
        . $this->quoteColumn($column) . ' '
        . $this->quoteColumn($column) . ' '
        . $type;
    }

    /**
     * @param $tableName
     * @param array $columns
     * @param array $rows
     * @return string
     */
    public function sqlInsert($tableName, array $columns = [], array $rows = [])
    {
        $sql = [];
        $columns = array_map(function ($column) {
            return $this->quoteColumn($column);
        }, $columns);

        foreach ($rows as $values) {
            $record = [];
            if (is_array($values) === false) {
                $values = [$values];
            }
            foreach ($values as $value) {
                if (is_string($value)) {
                    $value = $this->quoteValue($value);
                } elseif ($value === true) {
                    $value = 'TRUE';
                } elseif ($value === false) {
                    $value = 'FALSE';
                } elseif ($value === null) {
                    $value = 'NULL';
                }

                $record[] = $value;
            }
            $sql[] = '(' . implode(',', $record) . ')';
        }
        return $this->quoteSql('INSERT INTO ' . $this->quoteTableName($tableName) . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $sql)) . ')';
    }

    public function sqlUpdate($tableName, array $columns)
    {
        $updateSQL = [];
        foreach ($columns as $column => $value) {
            $updateSQL[] = $this->quoteColumn($column) . '=' . ($value instanceof Expression ? $value->toSQL() : $this->quoteValue($value));
        }

        return 'UPDATE ' . $this->quoteTableName($tableName) . ' SET ' . implode(' ', $updateSQL);
    }

    public function generateSelectSQL($select, $from, $where, $order, $group, $limit, $offset, $join, $having, $union)
    {
        if (empty($order)) {
            $orderColumns = [];
            $orderOptions = null;
        } else {
            list($orderColumns, $orderOptions) = $order;
        }

        $where = $this->sqlWhere($where);
        $orderSql = $this->sqlOrderBy($orderColumns, $orderOptions);
        $unionSql = $this->sqlUnion($union);

        return strtr('{select}{from}{join}{where}{group}{having}{order}{limit_offset}{union}', [
            '{select}' => $this->sqlSelect($select),
            '{from}' => $this->sqlFrom($from),
            '{where}' => $where,
            '{group}' => $this->sqlGroupBy($group),
            '{order}' => empty($union) ? $orderSql : '',
            '{having}' => $this->sqlHaving($having),
            '{join}' => $join,
            '{limit_offset}' => $this->sqlLimitOffset($limit, $offset),
            '{union}' => empty($union) ? '' : $unionSql . $orderSql
        ]);
    }


    /**
     * @param $tableName
     * @param array $columns
     * @param null $options
     * @return string
     */
    public function sqlCreateTable($tableName, $columns, $options = null)
    {
        if (is_array($columns)) {
            $cols = [];
            foreach ($columns as $name => $type) {
                if (is_string($name)) {
                    $cols[] = "\t" . $this->quoteColumn($name) . ' ' . $type;
                } else {
                    $cols[] = "\t" . $type;
                }
            }
            $sql = "CREATE TABLE " . $this->quoteTableName($tableName) . " (\n" . implode(",\n", $cols) . "\n)";
        } else {
            $sql = "CREATE TABLE " . $this->quoteTableName($tableName) . " " . $this->quoteSql($columns);
        }
        return empty($options) ? $sql : $sql . ' ' . $options;
    }

    /**
     * @param $tableName
     * @param array $columns
     * @param null $options
     * @return string
     */
    public function sqlCreateTableIfNotExists($tableName, $columns, $options = null)
    {
        if (is_array($columns)) {
            $cols = [];
            foreach ($columns as $name => $type) {
                if (is_string($name)) {
                    $cols[] = "\t" . $this->quoteColumn($name) . ' ' . $type;
                } else {
                    $cols[] = "\t" . $type;
                }
            }
            $sql = "CREATE TABLE IF NOT EXISTS " . $this->quoteTableName($tableName) . " (\n" . implode(",\n", $cols) . "\n)";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS " . $this->quoteTableName($tableName) . " " . $this->quoteSql($columns);
        }
        return empty($options) ? $sql : $sql . ' ' . $options;
    }

    /**
     * @param $oldTableName
     * @param $newTableName
     * @return string
     */
    abstract public function sqlRenameTable($oldTableName, $newTableName);

    /**
     * @param $tableName
     * @return string
     */
    public function sqlDropTable($tableName)
    {
        return "DROP TABLE " . $this->quoteSql($this->quoteTableName($tableName));
    }

    /**
     * @param $tableName
     * @return string
     */
    abstract public function sqlDropTableIfExists($tableName);

    /**
     * @param $tableName
     * @return string
     */
    abstract public function sqlTruncateTable($tableName);

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    abstract public function sqlDropIndex($tableName, $name);

    /**
     * @param $tableName
     * @param $column
     * @return string
     */
    abstract public function sqlDropColumn($tableName, $column);

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     * @return mixed
     */
    abstract public function sqlRenameColumn($tableName, $oldName, $newName);

    /**
     * @param $tableName
     * @param $name
     * @return mixed
     */
    abstract public function sqlDropForeignKey($tableName, $name);

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
    abstract public function sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null);

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     * @return string
     */
    abstract public function sqlAddPrimaryKey($tableName, $name, $columns);

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    abstract public function sqlDropPrimaryKey($tableName, $name);

    /**
     * @return string
     */
    abstract public function getRandomOrder();

    /**
     * @param $value
     * @return string
     */
    abstract public function getBoolean($value = null);

    /**
     * @param null $value
     * @return string
     */
    abstract public function getDateTime($value = null);

    /**
     * @param null $value
     * @return string
     */
    abstract public function getDate($value = null);

    /**
     * @param null $value
     * @return mixed
     */
    public function getTimestamp($value = null)
    {
        return $value instanceof \DateTime ? $value->getTimestamp() : strtotime($value);
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    abstract public function sqlAddColumn($tableName, $column, $type);

    /**
     * @param $tableName
     * @param $name
     * @param array $columns
     * @param bool $unique
     * @return string
     */
    abstract public function sqlCreateIndex($tableName, $name, array $columns, $unique = false);

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
        foreach ($tables as $tableAlias => $table) {
            if (strpos($table, 'SELECT') !== false) {
                $quotedTableNames[] = '(' . $table . ')' . (is_numeric($tableAlias) ? '' : ' AS ' . $this->quoteTableName($tableAlias));
            } else {
                $quotedTableNames[] = $this->quoteTableName($table) . (is_numeric($tableAlias) ? '' : ' AS ' . $this->quoteTableName($tableAlias));
            }
        }

        return ' FROM ' . implode(', ', $quotedTableNames);
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
            return $joinType . ' (' . $this->quoteSql($tableName) . ')' . (empty($alias) ? '' : ' AS ' . $this->quoteColumn($alias)) . ' ON ' . implode(',', $onSQL);
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
        if (is_string($where)) {
            return $where;
        }

        if (empty($where)) {
            return '';
        }

        if ($where instanceof Q) {
            $sql = $where->toSQL();
        } else {
            $sql = $this->quoteSql($where);
        }

        return empty($sql) ? '' : ' WHERE ' . $sql;
    }

    /**
     * @param $having
     * @return string
     */
    public function sqlHaving($having)
    {
        if (empty($having)) {
            return '';
        }

        if ($having instanceof Q) {
            $sql = $having->toSQL();
        } else {
            $sql = $this->quoteSql($having);
        }

        return empty($sql) ? '' : ' HAVING ' . $sql;
    }

    /**
     * @param $unions
     * @return string
     */
    public function sqlUnion($unions)
    {
        if (empty($unions)) {
            return '';
        }

        if (is_string($unions)) {
            return trim($unions);
        }

        $sql = [];
        foreach ($unions as $unionEntry) {
            list($union, $all) = $unionEntry;
            if ($union instanceof QueryBuilder) {
                $unionSQL = $union->toSQL();
            } else {
                $unionSQL = $union;
            }
            $sql[] = ($all ? 'UNION ALL' : 'UNION') . ' (' . $unionSQL . ')';
        }
        return ' ' . implode(' ', $sql);
    }

    /**
     * @param $tableName
     * @param $sequenceName
     * @return string
     */
    abstract public function sqlResetSequence($tableName, $sequenceName);

    /**
     * @param bool $check
     * @param string $schema
     * @param string $table
     * @return string
     */
    abstract public function sqlCheckIntegrity($check = true, $schema = '', $table = '');

    /**
     * @param $columns
     * @return string
     */
    public function sqlGroupBy($columns)
    {
        if (empty($columns)) {
            return '';
        }

        $group = [];
        foreach ($columns as $column) {
            $group[] = $this->quoteColumn($column);
        }

        return ' GROUP BY ' . implode(' ', $group);
    }

    /**
     * @param $columns
     * @param null $options
     * @return string
     */
    public function sqlOrderBy($columns, $options = null)
    {
        if (empty($columns)) {
            return '';
        }

        $order = [];
        foreach ($columns as $column) {
            if (strpos($column, '-', 0) === 0) {
                $column = substr($column, 1);
                $direction = 'DESC';
            } else {
                $direction = 'ASC';
            }

            $order[] = $this->quoteColumn($column) . ' ' . $direction;
        }

        return ' ORDER BY ' . implode(' ', $order) . (empty($options) ? '' : ' ' . $options);
    }

    /**
     * @param $columns
     * @return string
     */
    public function sqlSelect($columns)
    {
        if (empty($columns)) {
            return 'SELECT *';
        }

        if (is_array($columns) === false) {
            $columns = [$columns];
        }

        $select = [];
        foreach ($columns as $column => $subQuery) {
            if ($subQuery instanceof QueryBuilder) {
                $subQuery = $subQuery->toSQL();
            } else if ($subQuery instanceof Expression) {
                $subQuery = $this->quoteSql($subQuery->toSQL());
            } else {
                $subQuery = $this->quoteSql($subQuery);
            }

            if (is_numeric($column)) {
                $column = $subQuery;
                $subQuery = '';
            }

            if (!empty($subQuery)) {
                if (strpos($subQuery, 'SELECT') !== false) {
                    $value = '(' . $subQuery . ') AS ' . $this->quoteColumn($column);
                } else {
                    $value = $this->quoteColumn($column) . ' AS ' . $subQuery;
                }
            } else {
                if (strpos($column, ',') !== false) {
                    $newSelect = [];
                    foreach (explode(',', $column) as $item) {
                        // if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $item, $matches)) {
                        //     list(, $rawColumn, $rawAlias) = $matches;
                        // }

                        if (strpos($item, 'AS') !== false) {
                            list($rawColumn, $rawAlias) = explode('AS', $item);
                        } else {
                            $rawColumn = $item;
                            $rawAlias = '';
                        }

                        $newSelect[] = empty($rawAlias) ? $this->quoteColumn(trim($rawColumn)) : $this->quoteColumn(trim($rawColumn)) . ' AS ' . $this->quoteColumn(trim($rawAlias));
                    }
                    $value = implode(', ', $newSelect);

                } else {
                    $value = $this->quoteColumn($column);
                }
            }
            $select[] = $value;
        }

        return 'SELECT ' . implode(', ', $select);
    }

    public function generateInsertSQL($tableName, $columns, $rows)
    {
        return $this->sqlInsert($tableName, $columns, $rows);
    }

    public function generateDeleteSQL($from, $where)
    {
        return strtr('{delete}{from}{where}', [
            '{delete}' => 'DELETE',
            '{from}' => $this->sqlFrom($from),
            '{where}' => $this->sqlWhere($where)
        ]);
    }

    public function generateUpdateSQL($tableName, $update, $where)
    {
        return strtr('{update}{where}', [
            '{update}' => $this->sqlUpdate($tableName, $update),
            '{where}' => $this->sqlWhere($where),
        ]);
    }

    public function generateCreateTable($tableName, $columns, $options = null)
    {
        return $this->quoteSql($this->sqlCreateTable($this->quoteTableName($tableName), $columns, $options));
    }

    public function generateCreateTableIfNotExists($tableName, $columns, $options = null)
    {
        return $this->quoteSql($this->sqlCreateTableIfNotExists($this->quoteTableName($tableName), $columns, $options));
    }
}