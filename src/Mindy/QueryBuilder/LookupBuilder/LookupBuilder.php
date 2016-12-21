<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:39.
 */

namespace Mindy\QueryBuilder\LookupBuilder;

use Exception;
use Mindy\QueryBuilder\QueryBuilder;

class LookupBuilder extends Base
{
    public function parseLookup(QueryBuilder $queryBuilder, $rawLookup, $value)
    {
        if (substr_count($rawLookup, $this->separator) > 1) {
            if (empty($this->callback)) {
                throw new Exception('Unknown lookup: '.$rawLookup);
            } else {
                return $this->runCallback($queryBuilder, explode($this->separator, $rawLookup), $value);
            }
        }

        if (substr_count($rawLookup, $this->separator) == 0) {
            $rawLookup = $this->fetchColumnName($rawLookup);

            return [$this->default, $rawLookup, $value];
        } else {
            $lookupNodes = explode($this->separator, $rawLookup);
            if ($this->hasLookup(end($lookupNodes)) && substr_count($rawLookup, $this->separator) == 1) {
                list($column, $lookup) = explode($this->separator, $rawLookup);
                if ($this->hasLookup($lookup) == false) {
                    throw new Exception('Unknown lookup:'.$lookup);
                }
                $column = $this->fetchColumnName($column);

                return [$lookup, $column, $value];
            } else {
                return $this->runCallback($queryBuilder, $lookupNodes, $value);
            }
        }
    }

    public function buildJoin(QueryBuilder $queryBuilder, $lookup)
    {
        if (substr_count($lookup, $this->getSeparator()) > 0) {
            return $this->runJoinCallback($queryBuilder, explode($this->getSeparator(), $lookup));
        }

        return false;
    }

    public function parse(QueryBuilder $queryBuilder, array $where)
    {
        $conditions = [];
        foreach ($where as $lookup => $value) {
            /*
             * Parse new QOr([[username => 1], [username => 2]])
             */
            if (is_numeric($lookup) && is_array($value)) {
                $lookup = key($value);
                $value = array_shift($value);
            }
            $conditions[] = $this->parseLookup($queryBuilder, $lookup, $value);
        }

        return $conditions;
    }
}
