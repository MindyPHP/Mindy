<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 14:55
 */

namespace Mindy\QueryBuilder;

/**
 * Interface ILookupBuilder
 * @package Mindy\QueryBuilder
 */
interface ILookupBuilder
{
    public function setCollection(ILookupCollection $collection);

    /**
     * @param array $where
     * @return LegacyLookupBuilder|LookupBuilder
     */
    public function setWhere(array $where);
}