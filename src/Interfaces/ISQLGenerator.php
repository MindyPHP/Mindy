<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 30/06/16
 * Time: 16:07
 */

namespace Mindy\QueryBuilder\Interfaces;


interface ISQLGenerator
{
    /**
     * @param $value
     * @return bool
     */
    public function hasLimit($value);

    /**
     * @param $value
     * @return bool
     */
    public function hasOffset($value);

    /**
     * @param $tableName
     * @param array $columns
     * @param null $options
     * @param bool $ifNotExists
     * @return string
     */
    public function sqlCreateTable($tableName, $columns, $options = null, $ifNotExists = false);

    /**
     * @param $oldTableName
     * @param $newTableName
     * @return string
     */
    public function sqlRenameTable($oldTableName, $newTableName);

    /**
     * @param $tableName
     * @return string
     */
    public function sqlDropTable($tableName, $ifExists);

    /**
     * @param $tableName
     * @return string
     */
    public function sqlTruncateTable($tableName);

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function sqlDropIndex($tableName, $name);

    /**
     * @param $tableName
     * @param $column
     * @return string
     */
    public function sqlDropColumn($tableName, $column);

    /**
     * @param $tableName
     * @param $oldName
     * @param $newName
     * @return mixed
     */
    public function sqlRenameColumn($tableName, $oldName, $newName);

    /**
     * @param $tableName
     * @param $name
     * @return mixed
     */
    public function sqlDropForeignKey($tableName, $name);

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
    public function sqlAddForeignKey($tableName, $name, $columns, $refTable, $refColumns, $delete = null, $update = null);

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    public function sqlAlterColumn($tableName, $column, $type);

    /**
     * @param $tableName
     * @param $name
     * @param $columns
     * @return string
     */
    public function sqlAddPrimaryKey($tableName, $name, $columns);

    /**
     * @param $tableName
     * @param $name
     * @return string
     */
    public function sqlDropPrimaryKey($tableName, $name);

    /**
     * @return string
     */
    public function getRandomOrder();

    /**
     * @param $value
     * @return string
     */
    public function getBoolean($value = null);

    /**
     * @param null $value
     * @return string
     */
    public function getDateTime($value = null);

    /**
     * @param null $value
     * @return string
     */
    public function getDate($value = null);

    /**
     * @param null $value
     * @return mixed
     */
    public function getTimestamp($value = null);

    /**
     * @param $limit
     * @param null $offset
     * @return mixed
     */
    public function sqlLimitOffset($limit = null, $offset = null);

    /**
     * @param $tableName
     * @param $column
     * @param $type
     * @return string
     */
    public function sqlAddColumn($tableName, $column, $type);

    /**
     * @param $tableName
     * @param $name
     * @param array $columns
     * @param bool $unique
     * @return string
     */
    public function sqlCreateIndex($tableName, $name, array $columns, $unique = false);

    /**
     * @param $tables
     * @return string
     */
    public function sqlFrom($tables);

    /**
     * @param $joinType string
     * @param $tableName string
     * @param $on string|array
     * @param $alias string
     * @return string
     */
    public function sqlJoin($joinType, $tableName, $on, $alias);

    /**
     * @param $where string|array
     * @return string
     */
    public function sqlWhere($where);

    /**
     * @param $having
     * @return string
     */
    public function sqlHaving($having);

    /**
     * @param $unions
     * @return string
     */
    public function sqlUnion($unions);

    /**
     * @param $tableName
     * @param $sequenceName
     * @return string
     */
    public function sqlResetSequence($tableName, $sequenceName);

    /**
     * @param bool $check
     * @param string $schema
     * @param string $table
     * @return string
     */
    public function sqlCheckIntegrity($check = true, $schema = '', $table = '');

    /**
     * @param $columns
     * @return string
     */
    public function sqlGroupBy($columns);

    /**
     * @param $columns
     * @param null $options
     * @return string
     */
    public function sqlOrderBy($columns, $options = null);

    /**
     * @param null|string|array $columns
     * @param null|string|array $distinct
     * @return string
     */
    public function sqlSelect($columns, $distinct = null);

    /**
     * @param $tableName
     * @param array $columns
     * @param array $rows
     * @return string
     */
    public function sqlInsert($tableName, array $columns = [], array $rows = []);

    /**
     * @param $tableName
     * @param array $columns
     * @return string
     */
    public function sqlUpdate($tableName, array $columns);
}