<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05/07/16
 * Time: 11:13.
 */

namespace Mindy\QueryBuilder\Aggregation;

class Sum extends Aggregation
{
    public function toSQL()
    {
        return 'SUM('.parent::toSQL().')'.(empty($this->alias) ? '' : ' AS [['.$this->alias.']]');
    }
}
