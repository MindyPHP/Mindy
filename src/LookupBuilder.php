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
    protected $where;

    protected $defaultLookup = 'exact';

    private $lookups = [
        'isnull',
        'lte',
        'lt',
        'gte',
        'gt',
        'exact',
        'contains',
        'icontains',
        'startswith',
        'istartswith',
        'endswith',
        'iendswith',
        'in',
        'range',
        'year',
        'month',
        'day',
        'week_day',
        'hour',
        'minute',
        'second',
        'search',
        'regex',
        'iregex',
        'raw',
    ];

    private $separator = '__';

    public function setWhere(array $where)
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
        $value = array_shift($data);
        return [$lookup, $column, $value];
    }

    public function generateCondition()
    {
        $conditions = [];
        foreach ($this->where as $lookup => $value) {
            if (is_array($value) == false) {
                $value = [$this->defaultLookup => $value];
            }
            $conditions[] = $this->parseLookup($lookup, $value);
        }
        return $conditions;
    }
}