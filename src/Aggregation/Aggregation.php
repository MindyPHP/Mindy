<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05/07/16
 * Time: 11:17
 */

namespace Mindy\QueryBuilder\Aggregation;

use Mindy\QueryBuilder\Expression;

class Aggregation extends Expression
{
    protected $alias;

    public function __construct($field, $alias = '')
    {
        parent::__construct($field);
        $this->alias = $alias;
    }
}