<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Pagination\DataSource;

interface DataSourceInterface
{
    /**
     * @param $source
     *
     * @return int
     */
    public function getTotal($source);

    /**
     * @param $source
     * @param $page
     * @param $pageSize
     *
     * @return array
     */
    public function applyLimit($source, $page, $pageSize);

    /**
     * @param $source
     *
     * @return bool
     */
    public function supports($source);
}
