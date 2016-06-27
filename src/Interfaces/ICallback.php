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
     * @return mixed
     */
    public function setQueryBuilder(QueryBuilder $qb);

    /**
     * @param ILookupBuilder $lookupBuilder
     * @return mixed
     */
    public function setLookupBuilder(ILookupBuilder $lookupBuilder);

    /**
     * @param $lookupNodes
     * @param $value
     * @return mixed
     */
    public function fetch(array $lookupNodes, $value);
}