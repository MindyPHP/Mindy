<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/06/16
 * Time: 13:00
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\QueryBuilder\LookupBuilder\Legacy;
use Mindy\QueryBuilder\Mysql\Adapter;
use Mindy\QueryBuilder\Mysql\LookupCollection;
use Mindy\QueryBuilder\QueryBuilder;
use PDO;

class PDOQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected function createPDOInstance()
    {
        return new PDO('mysql:host=127.0.0.1;dbname=test;charset=utf8', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function testSimple()
    {
        $pdo = $this->createPDOInstance();

        $collection = new LookupCollection();
        $lookupBuilder = new Legacy($collection->getLookups());
        $qb = new QueryBuilder(new Adapter($pdo), $lookupBuilder);
        $deleteSQL = $qb->setType(QueryBuilder::TYPE_DELETE)->setFrom('test')->toSQL();
        $pdo->query($deleteSQL)->execute();

        $qb = new QueryBuilder(new Adapter($pdo), $lookupBuilder);
        $qb->setSelect('COUNT(*)')->setFrom('test');
        $this->assertEquals($qb->toSQL(), 'SELECT COUNT(*) FROM `test`');
        $this->assertEquals(0, $pdo->query($qb->toSQL())->fetchColumn());

        $qb = new QueryBuilder(new Adapter($pdo), $lookupBuilder);
        $insertSQL = $qb
            ->setType(QueryBuilder::TYPE_INSERT)
            ->setInsert([['name' => 'foo']])
            ->setFrom('test')
            ->toSQL();
        $pdo->query($insertSQL)->execute();
    }
}