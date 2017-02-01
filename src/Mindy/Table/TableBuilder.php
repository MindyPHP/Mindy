<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Component\Table;

class TableBuilder
{
    protected $columns = [];

    protected $options = [];

    /**
     * TableBuilder constructor.
     *
     * @param TableFactory $factory
     * @param array        $options
     */
    public function __construct($factory, array $options = [])
    {
        $this->factory = $factory;
        $this->options = $options;
    }

    public function add($name, $column, array $options = [])
    {
        $this->columns[$name] = [
            'column' => $column,
            'options' => array_merge($options, ['name' => $name]),
        ];

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getTable()
    {
        $columns = [];
        foreach ($this->columns as $name => $params) {
            $columns[$name] = $this->factory->createColumn($params['column'], $params['options']);
        }

        return new Table($columns);
    }
}
