<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/07/16
 * Time: 10:36
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Mysql\Adapter;

class MysqlSchemaTest extends SchemaTest
{
    protected function getAdapter()
    {
        return new Adapter($this->createDriver());
    }
}