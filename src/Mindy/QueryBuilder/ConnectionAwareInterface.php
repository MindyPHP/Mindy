<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder;

use Doctrine\DBAL\Connection;

interface ConnectionAwareInterface
{
    /**
     * @param Connection $connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection);

    /**
     * @return Connection
     */
    public function getConnection();
}
