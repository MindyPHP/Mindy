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

    protected function parseLookup($rawLookup, $value)
    {
        if (substr_count($rawLookup, $this->separator) > 0) {
            list($column, $lookup) = explode($this->separator, $rawLookup);
            if (in_array($lookup, $this->lookups) == false) {
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
            $conditions[] = $this->parseLookup($lookup, $value);
        }
        return $conditions;
    }
}