<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     *
     * @param string $name table name
     *
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, '`') !== false ? $name : '`'.$name.'`';
    }

    /**
     * Quotes a column name for use in a query.
     * A simple column name has no prefix.
     *
     * @param string $name column name
     *
     * @return string the properly quoted column name
     */
    public function quoteSimpleColumnName($name)
    {
        return strpos($name, '`') !== false || $name === '*' ? $name : '`'.$name.'`';
    }

    public function getLookupCollection()
    {
        return new LookupCollection();
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
     *
     * @return string
     */
    public function sqlRenameTable($oldTableName, $newTableName)
    {
        return 'ALTER TABLE '.$this->quoteTableName($oldTableName).' RENAME TO '.$this->quoteTableName($newTableName);
    }

    /**
     * @param $tableName
     * @param bool $cascade
     *
     * @return string
     */
    public function sqlTruncateTable($tableName, $cascade = false)
    {
        return 'DELETE FROM '.$this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @param $name
     *
     * @return string
     */
    public function sqlDropIndex($tableName, $name)
    {
        return 'DROP INDEX '.$this->quoteColumn($name);
    }

    /**
     * @param $tableName
     * @param $column
     *
     * @throws Exception
     *
     * @return string
     */
    public function sqlDropColumn($tableName, $column)
    {
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     *
     * @throws Exception
     *
     * @return string
     */
    public function sqlRenameColumn($tableName, $oldName, $newName)
    {
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $name
     *
     * @throws Exception
     *
     * @return string
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
     *
     * @throws Exception
     *
     * @return string
     */
    public function sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     *
     * @throws Exception
     *
     * @return string
     */
    public function sqlAlterColumn($tableName, $column, $type)
    {
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     *
     * @throws Exception
     *
     * @return string
     */
    public function sqlAddPrimaryKey($tableName, $name, $columns)
    {
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $tableName
     * @param $name
     *
     * @throws Exception
     *
     * @return string
     */
    public function sqlDropPrimaryKey($tableName, $name)
    {
        throw new NotSupportedException('not supported by SQLite');
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getBoolean($value = null)
    {
        return (bool) $value ? 1 : 0;
    }

    /**
     * @param $value string|\DateTime
     * @param $format string
     *
     * @return string
     */
    protected function formatDateTime($value, $format)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format($format);
        } elseif ($value === null) {
            $value = date($format);
        } elseif (is_numeric($value)) {
            $value = date($format, $value);
        } elseif (is_string($value)) {
            $value = date($format, strtotime($value));
        }

        return (string) $value;
    }

    /**
     * @param null $value
     *
     * @return string
     */
    public function getDateTime($value = null)
    {
        return $this->formatDateTime($value, 'Y-m-d H:i:s');
    }

    /**
     * @param null $value
     *
     * @return string
     */
    public function getDate($value = null)
    {
        return $this->formatDateTime($value, 'Y-m-d');
    }

    /**
     * @param $limit
     * @param null $offset
     *
     * @return mixed
     */
    public function sqlLimitOffset($limit = null, $offset = null)
    {
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT '.$limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET '.$offset;
            }

            return ' '.$sql;
        } elseif ($this->hasOffset($offset)) {
            // limit is not optional in SQLite
            // http://www.sqlite.org/syntaxdiagrams.html#select-stmt
            // If the LIMIT expression evaluates to a negative value, then there
            // is no upper bound on the number of rows returned.
            // -1 or 9223372036854775807 2^63-1
            return ' LIMIT 9223372036854775807 OFFSET '.$offset; // 2^63-1
        }

        return '';
    }

    /**
     * @param $tableName
     * @param $column
     * @param $type
     *
     * @return string
     */
    public function sqlAddColumn($tableName, $column, $type)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' ADD COLUMN '.$this->quoteColumn($column).' '.$type;
    }

    /**
     * @param $sequenceName
     * @param $value
     *
     * @return string
     */
    public function sqlResetSequence($sequenceName, $value = null)
    {
        return 'UPDATE sqlite_sequence SET seq='.$this->quoteValue($value).' WHERE name='.$this->quoteValue($sequenceName);
    }

    /**
     * @param bool   $check
     * @param string $schema
     * @param string $table
     *
     * @return string
     */
    public function sqlCheckIntegrity($check = true, $schema = '', $table = '')
    {
        return 'PRAGMA foreign_keys='.$this->getBoolean($check);
    }
}
