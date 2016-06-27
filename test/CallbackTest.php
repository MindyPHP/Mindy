<?php

namespace Mindy\QueryBuilder\Tests;

use Adapter;
use Mindy\QueryBuilder\Callback;
use Mindy\QueryBuilder\Interfaces\ICallback;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\QueryBuilderFactory;
use Mindy\QueryBuilder\Sqlite\LookupCollection;

class Model
{
    public function getFields()
    {
        
    }
}

class FetchCallback extends Callback implements ICallback
{
    protected $model;

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function fetch($lookup, $value, $separator)
    {
        /** @var \Mindy\QueryBuilder\QueryBuilder $qb */
        $qb = $this->qb;
        $nodes = explode($separator, $lookup);
        $lookup = $this->lookupBuilder->getDefault();
        foreach ($nodes as $nodeName) {
            switch ($nodeName) {
                case 'products':
                    $qb->setJoin('LEFT JOIN', $nodeName, ['product_id' => 'id'], 'product');
                    break;
                case 'categories':
                    $qb->setJoin('LEFT JOIN', $nodeName, ['category.id' => 'product.category_id'], 'category');
                    break;
                case 'statuses':
                    $qb->setJoin('LEFT JOIN', $nodeName, ['status.id' => 'product.status_id'], 'status');
                    break;
                case 'name':
                    $column = $nodeName;
                    $lookup = $this->lookupBuilder->getDefault();
                    break;
                default:
                    $lookup = $nodeName;
                    break;
            }
        }

        return [$lookup, $column, $value];
    }
}

/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 15:28
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryBuilderFactory
     */
    public $factory;

    public function getAdapter()
    {
        return ;
    }

    protected function setUp()
    {
        parent::setUp();
        $collection = new LookupCollection();
        $adapter = new Adapter(null, []);
        $this->factory = new QueryBuilderFactory($adapter, new Legacy($collection->getLookups(), new FetchCallback));
    }

    protected function getQueryBuilder()
    {
        return $this->factory->getQueryBuilder();
    }

    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $qb->setTypeSelect()->setFrom('test')->setWhere([
            'products__categories__statuses__name__in' => ['foo', 'bar']
        ]);
        $this->assertEquals($qb->toSQL(), 'SELECT * FROM test WHERE name IN (foo,bar) LEFT JOIN products AS product ON product_id=id LEFT JOIN categories AS category ON category.id=product.category_id LEFT JOIN statuses AS status ON status.id=product.status_id');
    }
}