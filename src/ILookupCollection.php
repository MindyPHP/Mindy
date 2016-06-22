<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:20
 */

namespace Mindy\QueryBuilder;

interface ILookupCollection
{
    /**
     * @param array $collection
     * @return mixed
     */
    public function addCollection(array $collection);

    /**
     * @param $lookup
     * @return bool
     */
    public function has($lookup);

    /**
     * @param $lookup
     * @param $column
     * @param $value
     * @return mixed
     */
    public function run($adapter, $lookup, $column, $value);
}