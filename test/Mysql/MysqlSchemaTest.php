<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/07/16
 * Time: 10:36
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Mysql\Adapter;
use PDO;

class MysqlSchemaTest extends SchemaTest
{
    protected function createDriver()
    {
        return new PDO('mysql:host=127.0.0.1;dbname=test;charset=utf8', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    protected function getAdapter()
    {
        return new Adapter($this->createDriver());
    }
}