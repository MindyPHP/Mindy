<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Pagination;

use Mindy\Pagination\DataSource\DataSourceInterface;
use Mindy\Pagination\Handler\NativePaginationHandler;
use Mindy\Pagination\Handler\PaginationHandlerInterface;

/**
 * Class PaginationFactory.
 */
class PaginationFactory
{
    /**
     * @var DataSourceInterface[]
     */
    protected $dataSources = [];

    /**
     * @param array|DataSourceInterface|mixed $source
     * @param array                           $parameters
     * @param PaginationHandlerInterface      $handler
     *
     * @return Pagination
     */
    public function createPagination($source, array $parameters, PaginationHandlerInterface $handler)
    {
        $handler = $handler ? $handler : new NativePaginationHandler();

        return new Pagination($source, $parameters, $handler, $this->findDataSource($source));
    }

    /**
     * @param $source
     *
     * @return DataSourceInterface
     */
    protected function findDataSource($source)
    {
        foreach ($this->dataSources as $dataSource) {
            if ($dataSource->supports($source)) {
                return $dataSource;
            }
        }

        throw new \RuntimeException('Unknown source type');
    }

    /**
     * @param DataSourceInterface $dataSource
     */
    public function addDataSource(DataSourceInterface $dataSource)
    {
        $this->dataSources[] = $dataSource;
    }
}
