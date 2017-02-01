<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\QueryBuilder\Driver;

use Doctrine\DBAL\Driver\PDOSqlite\Driver;

class SqliteDriver extends Driver
{
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        $connect = parent::connect($params, $username, $password, $driverOptions);
        $connect->sqliteCreateFunction('REGEXP', 'preg_match', 2);

        return $connect;
    }
}
