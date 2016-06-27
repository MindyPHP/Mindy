<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 16:50
 */

namespace Mindy\QueryBuilder;

use Exception;

class LookupBuilder implements ILookupBuilder
{
    /**
     * @var array
     */
    protected $where = [];
    /**
     * @var string
     */
    protected $defaultLookup = 'exact';
    /**
     * @var ILookupCollection
     */
    protected $collection;
    /**
     * @var string
     */
    private $separator = '__';

    public function setWhere($where)
    {
        $this->where = $where;
        return $this;
    }

    protected function parseLookup($column, array $data)
    {
        if (substr_count($column, $this->separator) > 1) {
            throw new Exception('LookupBuilder not support nested column names');
        }

        $lookup = key($data);
        if ($this->collection->has($lookup) === false) {
            throw new Exception('Unknown lookup: ' . $lookup);
        }
        $value = array_shift($data);
        return [$lookup, $column, $value];
    }

    public function generateCondition()
    {
        $conditions = [];
        foreach ($this->where as $column => $data) {
            if (is_array($data) == false) {
                $data = [$this->defaultLookup => $data];
            }
            $conditions[] = $this->parseLookup($column, $data);
        }
        return $conditions;
    }

    public function setCollection(ILookupCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }
}