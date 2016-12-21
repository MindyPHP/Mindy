<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 15:20.
 */

namespace Mindy\QueryBuilder\Interfaces;

interface ILookupCollection
{
    /**
     * @param $lookup
     *
     * @return bool
     */
    public function has($lookup);

    /**
     * @param $lookup
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function process(IAdapter $adapter, $lookup, $column, $value);
}
