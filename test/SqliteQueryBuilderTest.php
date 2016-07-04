<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Sqlite\Adapter;

require_once 'DummyQueryBuilderTest.php';

class SqliteQueryBuilderTest extends DummyQueryBuilderTest
{
    public function getAdapter()
    {
        return new Adapter;
    }
}