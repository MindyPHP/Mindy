<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Pagination\DataSource;

use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;

class QuerySetDataSource implements DataSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTotal($source)
    {
        if ($source instanceof Manager) {
            $source = $source->getQuerySet();
        }
        $clone = clone $source;

        return $clone->count();
    }

    /**
     * {@inheritdoc}
     */
    public function applyLimit($source, $page, $pageSize)
    {
        if ($source instanceof Manager) {
            $source = $source->getQuerySet();
        }
        $clone = clone $source;

        return $clone->paginate($page, $pageSize)->all();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($source)
    {
        return $source instanceof QuerySet || $source instanceof Manager;
    }
}
