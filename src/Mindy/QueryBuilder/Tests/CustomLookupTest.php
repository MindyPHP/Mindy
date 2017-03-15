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
     * @throws Exception
     *
     * @return string
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
