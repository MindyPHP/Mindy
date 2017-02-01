<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Component\Table;

use InvalidArgumentException;
use Mindy\Component\Table\Column\ColumnInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Registry implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $columns;
    protected $tables;

    public function __construct(array $tables = [], array $columns = [])
    {
        $this->tables = $tables;
        $this->columns = $columns;
    }

    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * Wraps a type into a ResolvedFormTypeInterface implementation and connects
     * it with its parent type.
     *
     * @param TableTypeInterface $type The type to resolve
     *
     * @return TableTypeInterface The resolved type
     */
    private function resolveType(TableTypeInterface $type)
    {
        $fqcn = get_class($type);

        return new $fqcn();
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($name)
    {
        if (!isset($this->tables[$name])) {
            $type = null;

            if (!$type) {
                // Support fully-qualified class names
                if (class_exists($name) && in_array(TableTypeInterface::class, class_implements($name))) {
                    $type = new $name();
                } else {
                    throw new InvalidArgumentException(sprintf('Could not load type "%s"', $name));
                }
            }

            $this->tables[$name] = $this->resolveType($type);
        }

        return $this->tables[$name];
    }

    /**
     * @param $name
     *
     * @return ColumnInterface
     */
    public function getColumn($name)
    {
        if ($this->hasColumn($name)) {
            $column = $this->columns[$name];

            return $this->container->get($column);
        } elseif (class_exists($name) && in_array(ColumnInterface::class, class_implements($name))) {
            return $this->columns[$name] = new $name();
        }

        throw new \RuntimeException(sprintf(
            'Unknown column type %s', $name
        ));
    }
}
