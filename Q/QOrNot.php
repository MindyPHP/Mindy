<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 01/07/16
 * Time: 12:10.
 */

namespace Mindy\QueryBuilder\Q;

use Mindy\QueryBuilder\QueryBuilder;

class QOrNot extends QOr
{
    public function toSQL(QueryBuilder $queryBuilder)
    {
        return 'NOT ('.parent::toSQL($queryBuilder).')';
    }
}
