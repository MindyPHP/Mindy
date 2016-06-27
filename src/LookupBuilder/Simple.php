<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 16:50
 */

namespace Mindy\QueryBuilder\LookupBuilder;

use Exception;

class Simple extends Base
{
    protected function parseLookup($column, array $data)
    {
        if (substr_count($column, $this->separator) > 1) {
            throw new Exception('LookupBuilder not support nested column names');
        }

        $lookup = key($data);
        if ($this->hasLookup($lookup) === false) {
            throw new Exception('Unknown lookup: ' . $lookup);
        }
        $value = array_shift($data);
        return [$lookup, $column, $value];
    }

    public function parse(array $where)
    {
        $conditions = [];
        foreach ($where as $column => $data) {
            if (is_array($data) == false) {
                $data = [$this->default => $data];
            }
            $conditions[] = $this->parseLookup($column, $data);
        }
        return $conditions;
    }
}