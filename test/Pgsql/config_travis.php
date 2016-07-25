<?php

return [
    'class' => '\Mindy\Query\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=test;port=5432;',
    'username' => 'root',
    'password' => '',
    'fixture' => __DIR__ . '/pgsql.sql',
];
