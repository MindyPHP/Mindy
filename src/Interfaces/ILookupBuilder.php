<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 14:55
 */

namespace Mindy\QueryBuilder\Interfaces;

/**
 * Interface ILookupBuilder
 * @package Mindy\QueryBuilder
 */
interface ILookupBuilder
{
    /**
     * @param array $where
     * @return mixed
     */
    public function parse(array $where);
}