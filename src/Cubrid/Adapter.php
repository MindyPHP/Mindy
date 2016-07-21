<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 21/07/16
 * Time: 15:07
 */

namespace Mindy\QueryBuilder\Cubrid;


use Mindy\QueryBuilder\BaseAdapter;
use Mindy\QueryBuilder\BaseLookupCollection;
use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;

class Adapter extends BaseAdapter implements IAdapter
{
    /**
     * @return BaseLookupCollection|ILookupCollection
     */
    public function getLookupCollection()
    {
        // TODO: Implement getLookupCollection() method.
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
            $tableName = $this->db->quoteTableName($tableName);
            if ($value === null) {
                $key = reset($table->primaryKey);
                $value = (int)$this->db->createCommand("SELECT MAX(`$key`) FROM " . $this->db->schema->quoteTableName($tableName))->queryScalar() + 1;
            } else {
                $value = (int)$value;
            }
            return "ALTER TABLE " . $this->db->schema->quoteTableName($tableName) . " AUTO_INCREMENT=$value;";
        } elseif ($table === null) {
            throw new InvalidParamException("Table not found: $tableName");
        } else {
            throw new InvalidParamException("There is not sequence associated with table '$tableName'.");
        }
    }

    /**
     * @inheritdoc
     */
    public function buildLimit($limit, $offset)
    {
        $sql = '';
        // limit is not optional in CUBRID
        // http://www.cubrid.org/manual/90/en/LIMIT%20Clause
        // "You can specify a very big integer for row_count to display to the last row, starting from a specific row."
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET ' . $offset;
            }
        } elseif ($this->hasOffset($offset)) {
            $sql = "LIMIT 9223372036854775807 OFFSET $offset"; // 2^63-1
        }
        return $sql;
    }
}