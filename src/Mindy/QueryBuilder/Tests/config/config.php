<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'mysql' => [
        'url' => 'mysql://root@127.0.0.1/test?charset=utf8',
        'driver' => 'pdo_mysql',
        'fixture' => __DIR__.'/../fixtures/mysql.sql',
    ],
    'pgsql' => [
        'dsn' => 'pgsql://root@localhost:5432/test',
        'driver' => 'pdo_pgsql',
        'fixture' => __DIR__.'/../fixtures/pgsql.sql',
    ],
    'sqlite' => [
        'url' => 'sqlite:///:memory:',
        'driver' => 'pdo_sqlite',
        'fixture' => __DIR__.'/../fixtures/sqlite.sql',
    ],
];
