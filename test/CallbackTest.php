<?php

namespace Mindy\QueryBuilder\Tests;

use Adapter;
use Exception;
use Mindy\QueryBuilder\Callback;
use Mindy\QueryBuilder\Interfaces\ICallback;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\QueryBuilderFactory;

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

    public function fetch(array $lookupNodes, $value)
    {
        /** @var \Mindy\QueryBuilder\QueryBuilder $qb */
        $qb = $this->qb;
        $lookup = $this->lookupBuilder->getDefault();
        foreach ($lookupNodes as $nodeName) {
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

        if (isset($column)) {
            return [$lookup, $column, $value];
        } else {
            throw new Exception('Unknown column');
        }
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

    protected function setUp()
    {
        parent::setUp();
        $adapter = new Adapter(null, []);
        $lookupBuilder = new Legacy($adapter->getLookupCollection()->getLookups(), new FetchCallback());
        $this->factory = new QueryBuilderFactory($adapter, $lookupBuilder);
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