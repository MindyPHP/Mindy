<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 01/07/16
 * Time: 12:10
 */

namespace Mindy\QueryBuilder\Q;

class QOrNot extends QOr
{
    public function toSQL()
    {
        return 'NOT (' . parent::toSQL() . ')';
    }
}