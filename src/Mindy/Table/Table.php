<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:02.
 */

namespace Mindy\Component\Table;

class Table
{
    protected $columns = [];
    protected $data = [];

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    protected function buildView()
    {
        return [
            'columns' => $this->columns,
            'rows' => $this->data,
        ];
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function createView()
    {
        $view = new TableView();
        $view->setData($this->buildView());

        return $view;
    }
}
