<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 17:31
 */

namespace Mindy\QueryBuilder\Interfaces;

interface ICallback
{
    /**
     * @param $lookup
     * @param $value
     * @return mixed
     */
    public function fetch($lookup, $value, $separator);
}