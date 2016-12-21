<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:07.
 */

namespace Mindy\Component\Table;

class TableView
{
    protected $data = [];

    /**
     * TableView constructor.
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
