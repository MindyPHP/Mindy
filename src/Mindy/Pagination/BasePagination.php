<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Pagination;

use Mindy\Pagination\DataSource\DataSourceInterface;
use Mindy\Pagination\Handler\PaginationHandlerInterface;

/**
 * Class BasePagination.
 */
abstract class BasePagination
{
    /**
     * @var int current pagination id
     */
    protected $id;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * @var int current page
     */
    protected $page;

    /**
     * @var string
     */
    protected $pageKey;

    /**
     * @var int default page size
     */
    protected $pageSize = 10;

    /**
     * @var string
     */
    protected $pageSizeKey;

    /**
     * @var PaginationHandlerInterface
     */
    protected $handler;

    /**
     * @var DataSourceInterface
     */
    protected $dataSource;

    /**
     * @var mixed
     */
    protected $source;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int autoincrement pagination classes on the page
     */
    private static $_id = 0;
    /**
     * @var array
     */
    protected $pageSizes = [10, 25, 50, 100];

    /**
     * BasePagination constructor.
     *
     * @param $source
     * @param array                      $config
     * @param PaginationHandlerInterface $handler
     * @param DataSourceInterface        $dataSource
     */
    public function __construct($source, array $config, PaginationHandlerInterface $handler, DataSourceInterface $dataSource)
    {
        ++self::$_id;
        $this->id = self::$_id;

        $this->source = $source;
        $this->dataSource = $dataSource;

        foreach (['page', 'pageSize', 'pageSizes'] as $key) {
            if (array_key_exists($key, $config)) {
                $this->{$key} = $config[$key];
            }
        }

        $this->handler = $handler;

        if (null === $this->page) {
            $this->page = $handler->getPage($this->getPageKey(), 1);
        }

        $this->pageSize = $handler->getPageSize($this->getPageSizeKey(), $this->pageSize);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return PageSize.
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = (int) $pageSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageSizeKey()
    {
        return empty($this->pageSizeKey) ? $this->getPageKey().'_PageSize' : $this->pageSizeKey;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int number of pages
     */
    public function getPagesCount()
    {
        $total = $this->getTotal();
        if ($total > 0) {
            return (int) ceil($total / $this->getPageSize());
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return (int) $this->page;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = (int) $page;

        return $this;
    }

    /**
     * Apply limits to source.
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function paginate()
    {
        $this->total = $this->dataSource->getTotal($this->source);
        if (
            ($this->total > $this->getPageSize()) &&
            ceil($this->total / $this->getPageSize()) < $this->getPage()
        ) {
            $this->handler->wrongPageCallback();
        }

        $this->data = $this->dataSource->applyLimit(
            $this->source,
            $this->getPage(),
            $this->getPageSize()
        );

        return $this->data;
    }

    /**
     * @return string
     */
    public function getPageKey()
    {
        if ($this->pageKey === null) {
            return sprintf('Pager_%s', $this->id);
        }

        return $this->pageKey;
    }

    /**
     * @return array
     */
    public function getPageSizes()
    {
        return $this->pageSizes;
    }

    /**
     * @return PaginationView
     */
    public function createView()
    {
        return new PaginationView([
            'total' => $this->getTotal(),
            'page' => $this->getPage(),
            'page_sizes' => $this->getPageSizes(),
            'page_size' => $this->getPageSize(),
            'page_count' => $this->getPagesCount(),
            'page_key' => $this->getPageKey(),
            'page_size_key' => $this->getPageSizeKey(),
        ], $this->handler);
    }

    /**
     * Reset id counter.
     */
    public static function resetCounter()
    {
        self::$_id = 0;
    }
}
