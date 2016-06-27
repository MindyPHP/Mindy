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
     * @param array $lookups
     * @return mixed
     */
    public function setLookups(array $lookups);

    /**
     * @param array $where
     * @return mixed
     */
    public function parse(array $where);

    /**
     * @param ICallback $callback
     * @return mixed
     */
    public function setCallback(ICallback $callback);
}