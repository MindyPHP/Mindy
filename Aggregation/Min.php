<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05/07/16
 * Time: 11:13.
 */

namespace Mindy\QueryBuilder\Aggregation;

class Min extends Aggregation
{
    public function toSQL()
    {
        return 'MIN('.parent::toSQL().')'.(empty($this->alias) ? '' : ' AS [['.$this->alias.']]');
    }
}
