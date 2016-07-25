<?php

return [
    'class' => '\Mindy\Query\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=test;port=5432;',
    'username' => 'postgres',
    'password' => '',
    'fixture' => __DIR__ . '/pgsql.sql',
];
