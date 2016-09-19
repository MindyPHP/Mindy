<?php

return [
    'mysql' => [
        'class' => '\Mindy\Query\Connection',
        'dsn' => 'mysql:host=127.0.0.1;dbname=test',
        'username' => 'root',
        'password' => '',
        'fixture' => __DIR__ . '/mysql.sql',
    ],
    'pgsql' => [
        'class' => '\Mindy\Query\Connection',
        'dsn' => 'pgsql:host=localhost;dbname=test;port=5432;',
        'username' => 'postgres',
        'password' => '',
        'fixture' => __DIR__ . '/pgsql.sql',
    ],
    'sqlite' => [
        'class' => '\Mindy\Query\Connection',
        'dsn' => 'sqlite:' . __DIR__ . '/../sqlite.db',
        'fixture' => __DIR__ . '/sqlite.sql',
        'username' => '',
        'password' => ''
    ]
];