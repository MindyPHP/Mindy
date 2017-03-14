<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Pagination\DataSource;

/**
 * Class ArrayDataSource.
 */
class ArrayDataSource implements DataSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTotal($source)
    {
        return count($source);
    }

    /**
     * {@inheritdoc}
     */
    public function applyLimit($source, $page, $pageSize)
    {
        return array_slice($source, $pageSize * ($page <= 1 ? 0 : $page - 1), $pageSize);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($source)
    {
        return is_array($source);
    }
}
