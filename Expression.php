<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04/07/16
 * Time: 18:57.
 */

namespace Mindy\QueryBuilder;

class Expression
{
    private $expression = '';

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function toSQL()
    {
        return $this->expression;
    }
}
