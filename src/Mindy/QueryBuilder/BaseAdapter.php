<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

    public function __construct($driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
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
     *
     * @param string $name column name
     *
     * @return string the properly quoted column name
     *
     * @see quoteSimpleColumnName()
     */
    public function quoteColumn($name)
    {
        if (strpos($name, '(') !== false || strpos($name, '[[') !== false || strpos($name, '{{') !== false) {
            return $name;
        }
        if (($pos = strrpos($name, '.')) !== false) {
            $prefix = $this->quoteTableName(substr($name, 0, $pos)).'.';
            $name = substr($name, $pos + 1);
        } else {
            $prefix = '';
        }

        return $prefix.$this->quoteSimpleColumnName($name);
    }

    /**
     * Quotes a simple column name for use in a query.
     * A simple column name should contain the column name only without any prefix.
     * If the column name is already quoted or is the asterisk character '*', this method will do nothing.
     *
     * @param string $name column name
     *
     * @return string the properly quoted column name
     */
    public function quoteSimpleColumnName($name)
    {
        return strpos($name, '"') !== false || $name === '*' ? $name : '"'.$name.'"';
    }

    /**
     * Returns the actual name of a given table name.
     * This method will strip off curly brackets from the given table name
     * and replace the percentage character '%' with [[Connection::tablePrefix]].
     *
     * @param string $name the table name to be converted
     *
     * @return string the real name of the given table name
     */
    public function getRawTableName($name)
    {
        if (strpos($name, '{{') !== false) {
            $name = preg_replace('/\\{\\{(.*?)\\}\\}/', '\1', $name);

            return str_replace('%', $this->getTablePrefix(), $name);
        }

        return $name;
    }

    /**
     * @return null|\PDO
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param $pdo
     *
     * @return $this
     */
    public function setDriver($pdo)
    {
        $this->driver = $pdo;

        return $this;
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
     *
     * @return string the properly quoted string
     *
     * @see http://www.php.net/manual/en/function.PDO-quote.php
     */
    public function quoteValue($str)
    {
        if (!is_string($str)) {
            return $str;
        }

        return $this->getDriver()->quote($str);
    }

    /**
     * Quotes a table name for use in a query.
     * If the table name contains schema prefix, the prefix will also be properly quoted.
     * If the table name is already quoted or contains '(' or '{{',
     * then this method will do nothing.
     *
     * @param string $name table name
     *
     * @return string the properly quoted table name
     *
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
     *
     * @param string $name table name
     *
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, "'") !== false ? $name : "'".$name."'";
    }

    public function quoteSql($sql)
    {
        $tablePrefix = $this->tablePrefix;

        return preg_replace_callback('/(\\{\\{(%?[\w\-\. ]+%?)\\}\\}|\\[\\[([\w\-\. ]+)\\]\\])|\\@([\w\-\. \/\%\:]+)\\@/',
            function ($matches) use ($tablePrefix) {
                if (isset($matches[4])) {
                    return $this->quoteValue($this->convertToDbValue($matches[4]));
                } elseif (isset($matches[3])) {
                    return $this->quoteColumn($matches[3]);
                }

                return str_replace('%', $tablePrefix, $this->quoteTableName($matches[2]));
            }, $sql);
    }

    public function convertToDbValue($rawValue)
    {
        if ($rawValue === true || $rawValue === false || $rawValue === 'true' || $rawValue === 'false') {
            return $this->getBoolean($rawValue);
        } elseif ($rawValue === 'null' || $rawValue === null) {
            return 'NULL';
        }

        return $rawValue;
    }

    /**
     * Checks to see if the given limit is effective.
     *
     * @param mixed $limit the given limit
     *
     * @return bool whether the limit is effective
     */
    public function hasLimit($limit)
    {
        return (int) $limit > 0;
    }

    /**
     * Checks to see if the given offset is effective.
     *
     * @param mixed $offset the given offset
     *
     * @return bool whether the offset is effective
     */
    public function hasOffset($offset)
    {
        return (int) $offset > 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return string the LIMIT and OFFSET clauses
     */
    abstract public function sqlLimitOffset($limit = null, $offset = null);

    /**
     * @param $columns
     *
     * @return array|string
     */
    public function buildColumns($columns)
    {
        if (!is_array($columns)) {
            if ($columns instanceof Aggregation) {
                $columns->setFieldsSql($this->buildColumns($columns->getFields()));

                return $this->quoteSql($columns->toSQL());
            } elseif (strpos($columns, '(') !== false) {
                return $this->quoteSql($columns);
            }
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach ($columns as $i => $column) {
            if ($column instanceof Expression) {
                $columns[$i] = $this->quoteSql($column->toSQL());
            } elseif (strpos($column, 'AS') !== false) {
                if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $column, $matches)) {
                    list(, $rawColumn, $rawAlias) = $matches;
                    $columns[$i] = $this->quoteColumn($rawColumn).' AS '.$this->quoteColumn($rawAlias);
                }
            } elseif (strpos($column, '(') === false) {
                $columns[$i] = $this->quoteColumn($column);
            }
        }

        return is_array($columns) ? implode(', ', $columns) : $columns;
    }

    /**
     * Builds a SQL statement for adding a primary key constraint to an existing table.
     *
     * @param string       $name      the name of the primary key constraint
     * @param string       $tableName the table that the primary key constraint will be added to
     * @param string|array $columns   comma separated string or array of columns that the primary key will consist of
     *
     * @return string the SQL statement for adding a primary key constraint to an existing table
     */
    public function sqlAddPrimaryKey($tableName, $name, $columns)
    {
        if (is_string($columns)) {
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach ($columns as $i => $col) {
            $columns[$i] = $this->quoteColumn($col);
        }

        return 'ALTER TABLE '.$this->quoteTableName($tableName).' ADD CONSTRAINT '
        .$this->quoteColumn($name).' PRIMARY KEY ('.implode(', ', $columns).')';
    }

    /**
     * Builds a SQL statement for removing a primary key constraint to an existing table.
     *
     * @param string $name      the name of the primary key constraint to be removed
     * @param string $tableName the table that the primary key constraint will be removed from
     *
     * @return string the SQL statement for removing a primary key constraint from an existing table
     */
    public function sqlDropPrimaryKey($tableName, $name)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' DROP PRIMARY KEY '.$this->quoteColumn($name);
    }

    public function sqlAlterColumn($tableName, $column, $type)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' CHANGE '
        .$this->quoteColumn($column).' '
        .$this->quoteColumn($column).' '
        .$type;
    }

    /**
     * @param $tableName
     * @param array $rows
     *
     * @return string
     */
    public function sqlInsert($tableName, array $rows)
    {
        if (isset($rows[0]) && is_array($rows)) {
            $columns = array_map(function ($column) {
                return $this->quoteColumn($column);
            }, array_keys($rows[0]));

            $values = [];

            foreach ($rows as $row) {
                $record = [];
                foreach ($row as $value) {
                    if ($value instanceof Expression) {
                        $value = $value->toSQL();
                    } elseif ($value === true || $value === 'true') {
                        $value = 'TRUE';
                    } elseif ($value === false || $value === 'false') {
                        $value = 'FALSE';
                    } elseif ($value === null || $value === 'null') {
                        $value = 'NULL';
                    } elseif (is_string($value)) {
                        $value = $this->quoteValue($value);
                    }

                    $record[] = $value;
                }
                $values[] = '('.implode(', ', $record).')';
            }

            $sql = 'INSERT INTO '.$this->quoteTableName($tableName).' ('.implode(', ', $columns).') VALUES '.implode(', ', $values);

            return $this->quoteSql($sql);
        }
        $columns = array_map(function ($column) {
            return $this->quoteColumn($column);
        }, array_keys($rows));

        $values = array_map(function ($value) {
            if ($value instanceof Expression) {
                $value = $value->toSQL();
            } elseif ($value === true || $value === 'true') {
                $value = 'TRUE';
            } elseif ($value === false || $value === 'false') {
                $value = 'FALSE';
            } elseif ($value === null || $value === 'null') {
                $value = 'NULL';
            } elseif (is_string($value)) {
                $value = $this->quoteValue($value);
            }

            return $value;
        }, $rows);

        $sql = 'INSERT INTO '.$this->quoteTableName($tableName).' ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).')';

        return $this->quoteSql($sql);
    }

    public function sqlUpdate($tableName, array $columns)
    {
        $tableName = $this->getRawTableName($tableName);
        $parts = [];
        foreach ($columns as $column => $value) {
            if ($value instanceof Expression) {
                $val = $this->quoteSql($value->toSQL());
            } else {
                // TODO refact, use getSqlType
                if ($value === 'true' || $value === true) {
                    $val = 'TRUE';
                } elseif ($value === null || $value === 'null') {
                    $val = 'NULL';
                } elseif ($value === false || $value === 'false') {
                    $val = 'FALSE';
                } else {
                    $val = $this->quoteValue($value);
                }
            }
            $parts[] = $this->quoteColumn($column).'='.$val;
        }

        return 'UPDATE '.$this->quoteTableName($tableName).' SET '.implode(', ', $parts);
    }

    /**
     * @param $select
     * @param $from
     * @param $where
     * @param $order
     * @param $group
     * @param $limit
     * @param $offset
     * @param $join
     * @param $having
     * @param $union
     * @param $distinct
     *
     * @return string
     */
    public function generateSelectSQL($select, $from, $where, $order, $group, $limit, $offset, $join, $having, $union, $distinct)
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
            '{select}' => $this->sqlSelect($select, $distinct),
            '{from}' => $this->sqlFrom($from),
            '{where}' => $where,
            '{group}' => $this->sqlGroupBy($group),
            '{order}' => empty($union) ? $orderSql : '',
            '{having}' => $this->sqlHaving($having),
            '{join}' => $join,
            '{limit_offset}' => $this->sqlLimitOffset($limit, $offset),
            '{union}' => empty($union) ? '' : $unionSql.$orderSql,
        ]);
    }

    /**
     * @param $tableName
     * @param array $columns
     * @param null  $options
     * @param bool  $ifNotExists
     *
     * @return string
     */
    public function sqlCreateTable($tableName, $columns, $options = null, $ifNotExists = false)
    {
        $tableName = $this->getRawTableName($tableName);
        if (is_array($columns)) {
            $cols = [];
            foreach ($columns as $name => $type) {
                if (is_string($name)) {
                    $cols[] = "\t".$this->quoteColumn($name).' '.$type;
                } else {
                    $cols[] = "\t".$type;
                }
            }
            $sql = ($ifNotExists ? 'CREATE TABLE IF NOT EXISTS ' : 'CREATE TABLE ').$this->quoteTableName($tableName)." (\n".implode(",\n", $cols)."\n)";
        } else {
            $sql = ($ifNotExists ? 'CREATE TABLE IF NOT EXISTS ' : 'CREATE TABLE ').$this->quoteTableName($tableName).' '.$this->quoteSql($columns);
        }

        return empty($options) ? $sql : $sql.' '.$options;
    }

    /**
     * @param $oldTableName
     * @param $newTableName
     *
     * @return string
     */
    abstract public function sqlRenameTable($oldTableName, $newTableName);

    /**
     * @param $tableName
     * @param bool $ifExists
     * @param bool $cascade
     *
     * @return string
     */
    public function sqlDropTable($tableName, $ifExists = false, $cascade = false)
    {
        $tableName = $this->getRawTableName($tableName);

        return ($ifExists ? 'DROP TABLE IF EXISTS ' : 'DROP TABLE ').$this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @param bool $cascade
     *
     * @return string
     */
    public function sqlTruncateTable($tableName, $cascade = false)
    {
        return 'TRUNCATE TABLE '.$this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @param $name
     *
     * @return string
     */
    abstract public function sqlDropIndex($tableName, $name);

    /**
     * @param $value
     *
     * @return string
     */
    public function getSqlType($value)
    {
        if ($value === 'true' || $value === true) {
            return 'TRUE';
        } elseif ($value === null || $value === 'null') {
            return 'NULL';
        } elseif ($value === false || $value === 'false') {
            return 'FALSE';
        }

        return $value;
    }

    /**
     * @param $tableName
     * @param $column
     *
     * @return string
     */
    public function sqlDropColumn($tableName, $column)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' DROP COLUMN '.$this->quoteColumn($column);
    }

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     *
     * @return mixed
     */
    abstract public function sqlRenameColumn($tableName, $oldName, $newName);

    /**
     * @param $tableName
     * @param $name
     *
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
     *
     * @return string
     */
    public function sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        $sql = 'ALTER TABLE '.$this->quoteTableName($tableName)
            .' ADD CONSTRAINT '.$this->quoteColumn($name)
            .' FOREIGN KEY ('.$this->buildColumns($columns).')'
            .' REFERENCES '.$this->quoteTableName($refTable)
            .' ('.$this->buildColumns($refColumns).')';
        if ($delete !== null) {
            $sql .= ' ON DELETE '.$delete;
        }
        if ($update !== null) {
            $sql .= ' ON UPDATE '.$update;
        }

        return $sql;
    }

    /**
     * @return string
     */
    abstract public function getRandomOrder();

    /**
     * @param $value
     *
     * @return string
     */
    abstract public function getBoolean($value = null);

    /**
     * @param null $value
     *
     * @return string
     */
    abstract public function getDateTime($value = null);

    /**
     * @param null $value
     *
     * @return string
     */
    abstract public function getDate($value = null);

    /**
     * @param null $value
     *
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
     *
     * @return string
     */
    abstract public function sqlAddColumn($tableName, $column, $type);

    /**
     * @param $tableName
     * @param $name
     * @param array $columns
     * @param bool  $unique
     *
     * @return string
     */
    public function sqlCreateIndex($tableName, $name, array $columns, $unique = false)
    {
        return ($unique ? 'CREATE UNIQUE INDEX ' : 'CREATE INDEX ')
        .$this->quoteTableName($name).' ON '
        .$this->quoteTableName($tableName)
        .' ('.$this->buildColumns($columns).')';
    }

    /**
     * @param $tables
     *
     * @return string
     */
    public function sqlFrom($tables)
    {
        if (empty($tables)) {
            return '';
        }

        if (!is_array($tables)) {
            $tables = (array) $tables;
        }
        $quotedTableNames = [];
        foreach ($tables as $tableAlias => $table) {
            if ($table instanceof QueryBuilder) {
                $tableRaw = $table->toSQL();
            } else {
                $tableRaw = $this->getRawTableName($table);
            }
            if (strpos($tableRaw, 'SELECT') !== false) {
                $quotedTableNames[] = '('.$tableRaw.')'.(is_numeric($tableAlias) ? '' : ' AS '.$this->quoteTableName($tableAlias));
            } else {
                $quotedTableNames[] = $this->quoteTableName($tableRaw).(is_numeric($tableAlias) ? '' : ' AS '.$this->quoteTableName($tableAlias));
            }
        }

        return implode(', ', $quotedTableNames);
    }

    /**
     * @param $joinType string
     * @param $tableName string
     * @param $on string|array
     * @param $alias string
     *
     * @return string
     */
    public function sqlJoin($joinType, $tableName, $on, $alias)
    {
        if (is_string($tableName)) {
            $tableName = $this->getRawTableName($tableName);
        } elseif ($tableName instanceof QueryBuilder) {
            $tableName = $tableName->toSQL();
        }

        $onSQL = [];
        if (is_string($on)) {
            $onSQL[] = $this->quoteSql($on);
        } else {
            foreach ($on as $leftColumn => $rightColumn) {
                if ($rightColumn instanceof Expression) {
                    $onSQL[] = $this->quoteColumn($leftColumn).'='.$this->quoteSql($rightColumn->toSQL());
                } else {
                    $onSQL[] = $this->quoteColumn($leftColumn).'='.$this->quoteColumn($rightColumn);
                }
            }
        }

        if (strpos($tableName, 'SELECT') !== false) {
            return $joinType.' ('.$this->quoteSql($tableName).')'.(empty($alias) ? '' : ' AS '.$this->quoteColumn($alias)).' ON '.implode(',', $onSQL);
        }

        return $joinType.' '.$this->quoteTableName($tableName).(empty($alias) ? '' : ' AS '.$this->quoteColumn($alias)).' ON '.implode(',', $onSQL);
    }

    /**
     * @param $where string|array
     *
     * @return string
     */
    public function sqlWhere($where)
    {
        if (empty($where)) {
            return '';
        }

        return ' WHERE '.$this->quoteSql($where);
    }

    /**
     * @param $having
     * @param QueryBuilder $qb
     *
     * @return string
     */
    public function sqlHaving($having, QueryBuilder $qb)
    {
        if (empty($having)) {
            return '';
        }

        if ($having instanceof Q) {
            $sql = $having->toSQL($qb);
        } else {
            $sql = $this->quoteSql($having);
        }

        return empty($sql) ? '' : ' HAVING '.$sql;
    }

    /**
     * @param $unions
     *
     * @return string
     */
    public function sqlUnion($union, $all = false)
    {
        if (empty($union)) {
            return '';
        }

        if ($union instanceof QueryBuilder) {
            $unionSQL = $union->order(null)->toSQL();
        } else {
            $unionSQL = $this->quoteSql($union);
        }

        return ($all ? 'UNION ALL' : 'UNION').' ('.$unionSQL.')';
    }

    /**
     * @param $tableName
     * @param $sequenceName
     *
     * @return string
     */
    abstract public function sqlResetSequence($tableName, $sequenceName);

    /**
     * @param bool   $check
     * @param string $schema
     * @param string $table
     *
     * @return string
     */
    abstract public function sqlCheckIntegrity($check = true, $schema = '', $table = '');

    /**
     * @param $columns
     *
     * @return string
     */
    public function sqlGroupBy($columns)
    {
        if (empty($columns)) {
            return '';
        }

        if (is_string($columns)) {
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
            $quotedColumns = array_map(function ($column) {
                return $this->quoteColumn($column);
            }, $columns);

            return implode(', ', $quotedColumns);
        }

        $group = [];
        foreach ($columns as $column) {
            $group[] = $this->quoteColumn($column);
        }

        return implode(', ', $group);
    }

    /**
     * @param $columns
     * @param null $options
     *
     * @return string
     */
    public function sqlOrderBy($columns, $options = null)
    {
        if (empty($columns)) {
            return '';
        }

        if (is_string($columns)) {
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
            $quotedColumns = array_map(function ($column) {
                $temp = explode(' ', $column);
                if (count($temp) == 2) {
                    return $this->quoteColumn($temp[0]).' '.$temp[1];
                }

                return $this->quoteColumn($column);
            }, $columns);

            return implode(', ', $quotedColumns);
        }

        $order = [];
        foreach ($columns as $key => $column) {
            if (is_numeric($key)) {
                if (strpos($column, '-', 0) === 0) {
                    $column = substr($column, 1);
                    $direction = 'DESC';
                } else {
                    $direction = 'ASC';
                }
            } else {
                $direction = $column;
                $column = $key;
            }

            $order[] = $this->quoteColumn($column).' '.$direction;
        }

        return implode(', ', $order).(empty($options) ? '' : ' '.$options);
    }

    /**
     * @param array|null|string $columns
     * @param null              $distinct
     *
     * @return string
     */
    public function sqlSelect($columns, $distinct = null)
    {
        $selectSql = $distinct ? 'SELECT DISTINCT ' : 'SELECT ';
        if (empty($columns)) {
            return $selectSql.'*';
        }

        if (is_array($columns) === false) {
            $columns = [$columns];
        }

        $select = [];
        foreach ($columns as $column => $subQuery) {
            if ($subQuery instanceof QueryBuilder) {
                $subQuery = $subQuery->toSQL();
            } elseif ($subQuery instanceof Expression) {
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
                    $value = '('.$subQuery.') AS '.$this->quoteColumn($column);
                } else {
                    $value = $this->quoteColumn($subQuery).' AS '.$this->quoteColumn($column);
                }
            } else {
                if (strpos($column, ',') === false && strpos($column, 'AS') !== false) {
                    if (strpos($column, 'AS') !== false) {
                        list($rawColumn, $rawAlias) = explode('AS', $column);
                    } else {
                        $rawColumn = $column;
                        $rawAlias = '';
                    }

                    $value = empty($rawAlias) ? $this->quoteColumn(trim($rawColumn)) : $this->quoteColumn(trim($rawColumn)).' AS '.$this->quoteColumn(trim($rawAlias));
                } elseif (strpos($column, ',') !== false) {
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

                        $newSelect[] = empty($rawAlias) ? $this->quoteColumn(trim($rawColumn)) : $this->quoteColumn(trim($rawColumn)).' AS '.$this->quoteColumn(trim($rawAlias));
                    }
                    $value = implode(', ', $newSelect);
                } else {
                    $value = $this->quoteColumn($column);
                }
            }
            $select[] = $value;
        }

        return $selectSql.implode(', ', $select);
    }

    public function generateInsertSQL($tableName, $values)
    {
        return $this->sqlInsert($tableName, $values);
    }

    public function generateDeleteSQL($from, $where)
    {
        return '';
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
        return $this->quoteSql($this->sqlCreateTable($this->quoteTableName($tableName), $columns, $options, true));
    }

    /**
     * Prepare value for db.
     *
     * @param $value
     *
     * @return int
     */
    public function prepareValue($value)
    {
        return $value;
    }
}
