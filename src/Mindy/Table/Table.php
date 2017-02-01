<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
