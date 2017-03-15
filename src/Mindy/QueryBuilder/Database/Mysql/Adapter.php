<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Database\Mysql;

use Exception;
use Mindy\QueryBuilder\BaseAdapter;
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

    /**
     * @return array
     */
    public function getLookupCollection()
    {
        return new LookupCollection();
    }

    public function getRandomOrder()
    {
        return 'RAND()';
    }

    /**
     * @param $oldTableName
     * @param $newTableName
     *
     * @return string
     */
    public function sqlRenameTable($oldTableName, $newTableName)
    {
        return 'RENAME TABLE '.$this->quoteTableName($oldTableName).' TO '.$this->quoteTableName($newTableName);
    }

    /**
     * Builds a SQL statement for removing a primary key constraint to an existing table.
     *
     * @param string $name  the name of the primary key constraint to be removed
     * @param string $table the table that the primary key constraint will be removed from
     *
     * @return string the SQL statement for removing a primary key constraint from an existing table
     */
    public function sqlDropPrimaryKey($table, $name)
    {
        return 'ALTER TABLE '.$this->quoteTableName($table).' DROP PRIMARY KEY';
    }

    /**
     * @param $tableName
     * @param $name
     *
     * @return string
     */
    public function sqlDropIndex($tableName, $name)
    {
        return 'DROP INDEX '.$this->quoteColumn($name).' ON '.$this->quoteTableName($tableName);
    }

    /**
     * @param $tableName
     * @param $name
     *
     * @return mixed
     */
    public function sqlDropForeignKey($tableName, $name)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' DROP FOREIGN KEY '.$this->quoteColumn($name);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getBoolean($value = null)
    {
        if (gettype($value) === 'boolean') {
            return (int) $value;
        }

        return $value ? 1 : 0;
    }

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

        return $value;
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
     * @param $tableName
     * @param $column
     * @param $type
     *
     * @return string
     */
    public function sqlAddColumn($tableName, $column, $type)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' ADD '.$this->quoteColumn($column).' '.$type;
    }

    /**
     * @param $tableName
     * @param $value
     *
     * @return string
     *
     * @internal param $sequenceName
     */
    public function sqlResetSequence($tableName, $value)
    {
        return 'ALTER TABLE '.$this->quoteTableName($tableName).' AUTO_INCREMENT='.$this->quoteValue($value);
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
        return 'SET FOREIGN_KEY_CHECKS = '.$this->getBoolean($check);
    }

    public function sqlLimitOffset($limit = null, $offset = null)
    {
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT '.$limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET '.$offset;
            }

            return ' '.$sql;
        } elseif ($this->hasOffset($offset)) {
            // limit is not optional in MySQL
            // http://stackoverflow.com/a/271650/1106908
            // http://dev.mysql.com/doc/refman/5.0/en/select.html#idm47619502796240
            return ' LIMIT '.$offset.', 18446744073709551615'; // 2^64-1
        }

        return '';
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
        $quotedTable = $this->quoteTableName($tableName);
        $row = $this->driver->query('SHOW CREATE TABLE '.$quotedTable)->fetch();
        if ($row === false) {
            throw new Exception("Unable to find column '$oldName' in table '$tableName'.");
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
                    return "ALTER TABLE {$quotedTable} CHANGE "
                    .$this->quoteColumn($oldName).' '
                    .$this->quoteColumn($newName).' '
                    .$matches[2][$i];
                }
            }
        }

        return "ALTER TABLE {$quotedTable} CHANGE ".$this->quoteColumn($oldName).' '.$this->quoteColumn($newName);
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
        if (gettype($value) === 'boolean') {
            return (int) $value;
        }

        return parent::prepareValue($value);
    }
}
