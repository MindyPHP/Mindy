<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05/07/16
 * Time: 11:13.
 */

namespace Mindy\QueryBuilder\Aggregation;

class Avg extends Aggregation
{
    public function toSQL()
    {
        return 'AVG('.parent::toSQL().')'.(empty($this->alias) ? '' : ' AS [['.$this->alias.']]');
    }
}
