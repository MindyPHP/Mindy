<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 17:31
 */

namespace Mindy\QueryBuilder\Interfaces;

use Mindy\QueryBuilder\QueryBuilder;

interface ICallback
{
    /**
     * @param QueryBuilder $qb
     * @param ILookupBuilder $lookupBuilder
     * @param array $lookupNodes
     * @param $value
     * @return mixed
     */
    public function fetch(QueryBuilder $qb, ILookupBuilder $lookupBuilder, array $lookupNodes, $value);
}