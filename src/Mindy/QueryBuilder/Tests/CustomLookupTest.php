<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 12:46.
 */

namespace Mindy\QueryBuilder\Tests;

use Exception;
use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;

class LookupLibrary implements ILookupCollection
{
    /**
     * @param $lookup
     *
     * @return bool
     */
    public function has($lookup)
    {
        return $lookup === 'foo';
    }

    /**
     * @param IAdapter $adapter
     * @param $lookup
     * @param $column
     * @param $value
     *
     * @return string
     *
     * @throws Exception
     */
    public function process(IAdapter $adapter, $lookup, $column, $value)
    {
        switch ($lookup) {
            case 'foo':
                return $adapter->quoteColumn($column).' ??? '.$adapter->quoteValue($value);

            default:
                throw new Exception('Unknown lookup: '.$lookup);
        }
    }
}

class CustomLookupTest extends BaseTest
{
    public function testCustom()
    {
        $qb = $this->getQueryBuilder();
        $qb->addLookupCollection(new LookupLibrary());
        list($lookup, $column, $value) = $qb->getLookupBuilder()->parseLookup($qb, 'name__foo', 1);
        $sql = $qb->getLookupBuilder()->runLookup($qb->getAdapter(), $lookup, $column, $value);
        $this->assertEquals($sql, '`name` ??? 1');
    }
}
