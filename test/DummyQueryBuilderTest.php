<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 10:25
 */

namespace Mindy\QueryBuilder\Tests;

use Adapter;
use Closure;
use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\Q\Q;
use Mindy\QueryBuilder\QueryBuilder;
use Mindy\QueryBuilder\QueryBuilderFactory;

class DummyQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryBuilderFactory
     */
    public $factory;

    public function getAdapter()
    {
        return new Adapter;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new QueryBuilderFactory($this->getAdapter(), new Legacy($this->getAdapter()->getLookupCollection()->getLookups()));
    }

    protected function tearDown()
    {
        $this->qb = null;
        parent::tearDown();
    }

    protected function getQueryBuilder()
    {
        return $this->factory->getQueryBuilder();
    }

    public function queryProvider()
    {
        $adapter = $this->getAdapter();
        $table = function ($tableName) use ($adapter) {
            return $adapter->quoteTableName($tableName);
        };
        $value = function ($value) use ($adapter) {
            return $adapter->quoteValue($value);
        };
        $column = function ($column) use ($adapter) {
            return $adapter->quoteColumn($column);
        };
        return [
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setFrom('tests')->toSQL();
                },
                'SELECT * FROM ' . $table('tests')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setFrom('comment')->setOrder(['id'])->setGroup(['id'])->toSQL();
                },
                'SELECT * FROM ' . $table('comment') . ' GROUP BY ' . $column('id') . ' ORDER BY ' . $column('id') . ' ASC'
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setAlias('t')->setFrom('comment')->setOrder(['id'])->setGroup(['id'])->toSQL();
                },
                'SELECT ' . $table('t') . '.* FROM ' . $table('comment') . ' AS ' . $table('t') . ' GROUP BY ' . $table('t') . '.' . $column('id') . ' ORDER BY ' . $table('t') . '.' . $column('id') . ' ASC'
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setFrom('comment')
                        ->setAlias('t')
                        ->setJoin('LEFT JOIN', 'user', ['t.user_id' => 'u.id'], 'u')->toSQL();
                },
                'SELECT ' . $table('t') . '.* FROM ' . $table('comment') . ' AS ' . $table('t') . ' LEFT JOIN ' . $table('user') . ' AS ' . $table('u') . ' ON ' . $column('t.user_id') . '=' . $column('u.id')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('t.id AS foo, t.user_id AS bar')->setFrom('comment')
                        ->setAlias('t')
                        ->setJoin('LEFT JOIN', 'user', ['t.user_id' => 'u.id'], 'u')->toSQL();
                },
                'SELECT ' . $table('t.id') . ' AS ' . $table('foo') . ',' . $table('t.user_id') . ' AS ' . $table('bar') . ' FROM ' . $table('comment') . ' AS ' . $table('t') . ' LEFT JOIN ' . $table('user') . ' AS ' . $table('u') . ' ON ' . $column('t.user_id') . '=' . $column('u.id')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setFrom('comment')->setJoin('LEFT JOIN', 'user', ['user_id' => 'id'])->toSQL();
                },
                'SELECT * FROM ' . $table('comment') . ' LEFT JOIN ' . $table('user') . ' ON ' . $column('user_id') . '=' . $column('id') . ''
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setFrom('comment')->setJoin('LEFT JOIN', 'user', ['user_id' => 'id'])->toSQL();
                },
                'SELECT * FROM ' . $table('comment') . ' LEFT JOIN ' . $table('user') . ' ON ' . $column('user_id') . '=' . $column('id')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setSelect('*')->setFrom('comment')
                        ->setJoin('LEFT JOIN', 'user', ['user_id' => 'id'])
                        ->setJoin('LEFT JOIN', 'group', ['group_id' => 'id'])
                        ->toSQL();
                },
                'SELECT * FROM ' . $table('comment') . ' LEFT JOIN ' . $table('user') . ' ON ' . $column('user_id') . '=' . $column('id') . ' LEFT JOIN ' . $table('group') . ' ON ' . $column('group_id') . '=' . $column('id')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setTypeInsert()->setAlias('t')->setInsert([
                        ['name' => 'qwe']
                    ])->setFrom('test')->toSQL();
                },
                'INSERT INTO ' . $table('test') . ' (' . $column('name') . ') VALUES (' . $value('qwe') . ')'
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setTypeUpdate()->setUpdate(['name' => 'bar'])->setWhere(['name' => 'foo'])->setFrom('test')->toSQL();
                },
                'UPDATE ' . $table('test') . ' SET ' . $column('name') . '=' . $value('bar') . ' WHERE ' . $column('name') . '=' . $value('foo')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setTypeDelete()->setWhere(['name' => 'qwe'])->setFrom('test')->toSQL();
                },
                'DELETE FROM ' . $table('test') . ' WHERE ' . $column('name') . '=' . $value('qwe')
            ],
            [
                function (QueryBuilder $qb) {
                    $qbSub = clone $qb;
                    $qbSub
                        ->setTypeSelect()->setFrom('comment')->setSelect('*')
                        ->setWhere(['is_published' => true]);

                    $qb->setTypeSelect()->setSelect([
                        'id' => $qbSub->toSQL()
                    ])->setFrom('test');
                    return $qb->toSQL();
                },
                'SELECT (SELECT * FROM ' . $table('comment') . ' WHERE ' . $column('is_published') . '=' . $value(1) . ') AS ' . $table('id') . ' FROM ' . $table('test')
            ],
            [
                function (QueryBuilder $qb) {
                    $qbSub = clone $qb;
                    $qbSub
                        ->setTypeSelect()->setFrom('comment')->setSelect('user_id')
                        ->setWhere(['is_published' => true]);
                    return $qb->setTypeSelect()->setAlias('t')->setSelect(['user_id'])->setFrom($qbSub->toSQL())->toSQL();
                },
                'SELECT ' . $column('t.user_id') . ' FROM (SELECT ' . $column('user_id') . ' FROM ' . $table('comment') . ' WHERE ' . $column('is_published') . '=' . $value(1) . ') AS ' . $table('t') . ''
            ],
            [
                function (QueryBuilder $qb) {
                    $qbSub = clone $qb;
                    $qbSub->setTypeSelect()->setFrom('user')->setSelect('id');
                    $subSql = $qbSub->toSQL();
                    return $qb->setTypeSelect()->setAlias('c')->setSelect(['*'])->setFrom('comment')
                        ->setJoin('INNER JOIN', $subSql, ['u.id' => 'c.user_id'], 'u')->toSQL();
                },
                'SELECT ' . $column('c') . '.* FROM ' . $table('comment') . ' AS ' . $table('c') . ' INNER JOIN (SELECT ' . $column('id') . ' FROM ' . $table('user') . ') AS ' . $table('u') . ' ON ' . $column('u.id') . '=' . $column('c.user_id')
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setTypeSelect()->setFrom('test')
                        ->setWhere([
                            'id' => 1,
                            new Q([
                                ['username' => 'foo'],
                                ['username' => 'bar']
                            ], 'OR')
                        ]);
                },
                'SELECT * FROM ' . $table('test') . ' WHERE ' . $column('id') . '=' . $value(1) . ' AND (' . $column('username') . '=' . $value('foo') . ' OR ' . $column('username') . '=' . $value('bar') . ')'
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb
                        ->setTypeSelect()
                        ->setFrom('test')
                        ->setWhere(['is_published' => true])
                        ->setExclude(['id' => 2])
                        ->addExclude(new Q([
                            ['username' => 'foo'],
                            ['username' => 'bar']
                        ], 'OR'));
                },
                'SELECT * FROM ' . $table('test') . ' WHERE ' . $column('is_published') . '=' . $value(1) . ' AND NOT (' . $column('id') . '=2 AND (' . $column('username') . '=' . $value('foo') . ' OR ' . $column('username') . '=' . $value('bar') . '))'
            ],
            [
                function (QueryBuilder $qb) {
                    return $qb->setFrom('test')->setOrder('id')->setLimit(10)->setOffset(10);
                },
                'SELECT * FROM ' . $table('test') . ' ORDER BY ' . $column('id') . ' ASC LIMIT 10 OFFSET 10'
            ]
        ];
    }

    /**
     * @dataProvider queryProvider
     */
    public function testQuery(Closure $callback, $sql)
    {
        $obj = $callback->__invoke($this->getQueryBuilder());
        $this->assertEquals($obj instanceof QueryBuilder ? $obj->toSQL() : $obj, $sql);
    }
}