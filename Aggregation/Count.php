<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05/07/16
 * Time: 11:13.
 */

namespace Mindy\QueryBuilder\Aggregation;

class Count extends Aggregation
{
    public function toSQL()
    {
        return 'COUNT('.parent::toSQL().')'.(empty($this->alias) ? '' : ' AS [['.$this->alias.']]');
    }
}
