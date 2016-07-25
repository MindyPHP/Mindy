<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25/07/16
 * Time: 16:53
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\Database\Sqlite\Adapter;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\QueryBuilder;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mindy\QueryBuilder\BaseAdapter
     */
    private $_adapter;

    /**
     * @return \Mindy\QueryBuilder\BaseAdapter
     */
    protected function getAdapter()
    {
        if ($this->_adapter === null) {
            $this->_adapter = new Adapter();
        }
        return $this->_adapter;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $adapter = $this->getAdapter();
        $builder = new Legacy();
        $builder->addLookupCollection($adapter->getLookupCollection());
        return new QueryBuilder($adapter, $builder);
    }

    /**
     * @param $sql
     * @return string
     */
    protected function quoteSql($sql)
    {
        return $this->getAdapter()->quoteSql($sql);
    }

    protected function assertSql($sql, $actual)
    {
        $this->assertEquals($this->quoteSql($sql), trim($actual));
    }
}