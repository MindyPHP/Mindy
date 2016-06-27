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
        $lookup = key($data);
        $value = array_shift($data);

        if (substr_count($column, $this->separator) > 1) {
            $this->callback->setLookupBuilder($this);
            $this->callback->setQueryBuilder($this->qb);
            return $this->callback->fetch($column, $value, $this->separator);
        } else {
            if ($this->hasLookup($lookup) === false) {
                if (empty($this->callback)) {
                    throw new Exception('Unknown lookup:' . $lookup);
                } else {
                    $this->callback->setLookupBuilder($this);
                    $this->callback->setQueryBuilder($this->qb);
                    return $this->callback->fetch($column, $value, $this->separator);
                }
            }
            return [$lookup, $column, $value];
        }
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