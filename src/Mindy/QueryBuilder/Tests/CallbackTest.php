<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Tests;

use Exception;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
use Mindy\QueryBuilder\QueryBuilder;

class CallbackTestCallback
{
    public function run(QueryBuilder $qb, ILookupBuilder $lookupBuilder, array $lookupNodes, $value)
    {
        $column = '?';
        $lookup = '?';
        foreach ($lookupNodes as $nodeName) {
            switch ($nodeName) {
                case 'products':
                    $qb->join('LEFT JOIN', $nodeName, ['t.product_id' => 'products.id'], 'products');
                    break;
                case 'categories':
                    $qb->join('LEFT JOIN', $nodeName, ['products.category_id' => 'categories.id'], 'categories');
                    break;
                case 'name':
                    $column = 'categories.'.$nodeName;
                    $lookup = $lookupBuilder->getDefault();
                    break;
                default:
                    $lookup = $nodeName;
                    break;
            }
        }

        return [$lookup, $column, $value];
    }
}

class CallbackTestTwoCallback
{
    public function run(QueryBuilder $qb, ILookupBuilder $lookupBuilder, array $lookupNodes, $value)
    {
        $lookup = $lookupBuilder->getDefault();
        foreach ($lookupNodes as $nodeName) {
            switch ($nodeName) {
                case 'products':
                    $qb->join('LEFT JOIN', $nodeName, ['product_id' => 'id'], 'products');
                    break;
                case 'categories':
                    $qb->join('LEFT JOIN', $nodeName, ['category.id' => 'product.category_id'], 'categories');
                    break;
                case 'statuses':
                    $qb->join('LEFT JOIN', $nodeName, ['status.id' => 'product.status_id'], 'statuses');
                    break;
                case 'name':
                    $column = $nodeName;
                    $lookup = $lookupBuilder->getDefault();
                    break;
                default:
                    $lookup = $nodeName;
                    break;
            }
        }

        if (isset($column)) {
            return [$lookup, $column, $value];
        }
        throw new Exception('Unknown column');
    }
}

/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 15:28.
 */
class CallbackTest extends BaseTest
{
    public function testSimple()
    {
        $qb = $this->getQueryBuilder();
        $qb->getLookupBuilder()->setCallback(new CallbackTestCallback());
        $this->assertTrue($qb->getLookupBuilder()->getCallback() instanceof CallbackTestCallback);
        $qb->from(['t' => 'test'])->where([
            'products__categories__name__in' => ['foo', 'bar'],
        ]);
        $sql = $qb->toSQL();
        $this->assertTrue($qb->hasJoin('products'));
        $this->assertEquals('SELECT * FROM test AS t LEFT JOIN products AS products ON t.product_id=products.id LEFT JOIN categories AS categories ON products.category_id=categories.id WHERE (categories.name IN (foo, bar))',
            str_replace(['`', "'"], '', $sql));
    }

    public function testHard()
    {
        $qb = $this->getQueryBuilder();
        $qb->getLookupBuilder()->setCallback(new CallbackTestTwoCallback());
        $this->assertTrue($qb->getLookupBuilder()->getCallback() instanceof CallbackTestTwoCallback);
        $qb->from('test')->where([
            'products__categories__statuses__name__in' => ['foo', 'bar'],
        ]);
        $sql = $qb->toSQL();
        $this->assertTrue($qb->hasJoin('products'));
        $this->assertTrue($qb->hasJoin('categories'));
        $this->assertTrue($qb->hasJoin('statuses'));
        $this->assertEquals('SELECT * FROM test LEFT JOIN products AS products ON product_id=id LEFT JOIN categories AS categories ON category.id=product.category_id LEFT JOIN statuses AS statuses ON status.id=product.status_id WHERE (name IN (foo, bar))',
            str_replace(['`', "'"], '', $sql));
    }
}
