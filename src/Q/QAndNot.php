<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 01/07/16
 * Time: 12:10
 */

namespace Mindy\QueryBuilder\Q;

class QAndNot extends QAnd
{
    public function toSQL()
    {
        return 'NOT (' . parent::toSQL() . ')';
    }
}