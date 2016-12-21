<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:03.
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
