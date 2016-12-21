<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:01.
 */

namespace Mindy\Component\Table;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class TableFactory implements TableFactoryInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * TableFactory constructor.
     *
     * @param Registry         $registry
     * @param PropertyAccessor $accessor
     * @param RouterInterface  $router
     */
    public function __construct(Registry $registry, PropertyAccessor $accessor, RouterInterface $router)
    {
        $this->registry = $registry;
        $this->accessor = $accessor;
        $this->router = $router;
    }

    /**
     * @param $type
     * @param null  $data
     * @param array $options
     *
     * @return TableBuilder
     */
    public function createBuilder($type = AbstractTableType::class, $data = null, array $options = array())
    {
        $type = $this->registry->getTable($type);
        $builder = new TableBuilder($this, $options);
        $type->buildTable($builder, $builder->getOptions());
        $type->setRouter($this->router);

        return $builder;
    }

    /**
     * @param $type
     * @param array $data
     * @param array $options
     *
     * @return Table
     */
    public function createTable($type, array $data = [], array $options = [])
    {
        $table = $this->createBuilder($type, $data, $options)->getTable();
        $table->setData($data);

        return $table;
    }

    /**
     * @param $column
     * @param array $options
     *
     * @return Column\ColumnInterface
     */
    public function createColumn($column, array $options = [])
    {
        $column = $this->registry->getColumn($column);
        $column->setOptions($options);
        $column->setPropertyAccessor($this->accessor);

        return $column;
    }
}
