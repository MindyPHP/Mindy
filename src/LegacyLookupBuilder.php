<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:39
 */

namespace Mindy\QueryBuilder;

use Exception;

class LegacyLookupBuilder implements ILookupBuilder
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

    protected function parseLookup($rawLookup, $value)
    {
        if (substr_count($rawLookup, $this->separator) > 0) {
            list($column, $lookup) = explode($this->separator, $rawLookup);
            if ($this->collection->has($lookup) == false) {
                throw new Exception('Unknown lookup:' . $lookup);
            }
            return [$lookup, $column, $value];
        } else {
            return [$this->defaultLookup, $rawLookup, $value];
        }
    }

    public function generateCondition()
    {
        $conditions = [];
        foreach ($this->where as $lookup => $value) {
            if (is_numeric($lookup)) {
                $lookup = key($value);
                $value = array_shift($value);
            }
            $conditions[] = $this->parseLookup($lookup, $value);
        }
        return $conditions;
    }

    public function setCollection(ILookupCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }
}