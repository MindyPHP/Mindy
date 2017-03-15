<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder;

use Mindy\QueryBuilder\Interfaces\IAdapter;
use Mindy\QueryBuilder\Interfaces\ILookupCollection;

class BaseLookupCollection implements ILookupCollection
{
    /**
     * @param $lookup
     *
     * @return bool
     */
    public function has($lookup)
    {
        return in_array($lookup, [
            'exact', 'gte', 'gt', 'lte', 'lt',
            'range', 'isnt', 'isnull', 'contains',
            'icontains', 'startswith', 'istartswith',
            'endswith', 'iendswith', 'in', 'raw',
        ]);
    }

    /**
     * @param IAdapter $adapter
     * @param $lookup
     * @param $column
     * @param $value
     *
     * @return string
     */
    public function process(IAdapter $adapter, $lookup, $column, $value)
    {
        switch ($lookup) {
            case 'exact':
                /* @var $adapter \Mindy\QueryBuilder\BaseAdapter */
                if ($value instanceof \DateTime) {
                    $value = $adapter->getDateTime($value);
                }

                if ($value instanceof Expression) {
                    $sqlValue = $value->toSQL();
                } elseif ($value instanceof QueryBuilder) {
                    $sqlValue = '('.$value->toSQL().')';
                } elseif (strpos($value, 'SELECT') !== false) {
                    $sqlValue = '('.$value.')';
                } else {
                    $sqlValue = $adapter->quoteValue($value);
                }

                return $adapter->quoteColumn($column).'='.$sqlValue;

            case 'gte':
                if ($value instanceof \DateTime) {
                    $value = $adapter->getDateTime($value);
                }

                return $adapter->quoteColumn($column).'>='.$adapter->quoteValue($value);

            case 'gt':
                if ($value instanceof \DateTime) {
                    $value = $adapter->getDateTime($value);
                }

                return $adapter->quoteColumn($column).'>'.$adapter->quoteValue($value);

            case 'lte':
                if ($value instanceof \DateTime) {
                    $value = $adapter->getDateTime($value);
                }

                return $adapter->quoteColumn($column).'<='.$adapter->quoteValue($value);

            case 'lt':
                if ($value instanceof \DateTime) {
                    $value = $adapter->getDateTime($value);
                }

                return $adapter->quoteColumn($column).'<'.$adapter->quoteValue($value);

            case 'range':
                list($min, $max) = $value;

                return $adapter->quoteColumn($column).' BETWEEN '.$adapter->quoteValue($min).' AND '.$adapter->quoteValue($max);

            case 'isnt':
                /** @var $adapter \Mindy\QueryBuilder\BaseAdapter */
                if (in_array($adapter->getSqlType($value), ['TRUE', 'FALSE', 'NULL'])) {
                    return $adapter->quoteColumn($column).' IS NOT '.$adapter->getSqlType($value);
                }

                    return $adapter->quoteColumn($column).'!='.$adapter->quoteValue($value);

            case 'isnull':
                return $adapter->quoteColumn($column).' '.((bool) $value ? 'IS NULL' : 'IS NOT NULL');

            case 'contains':
                return $adapter->quoteColumn($column).' LIKE '.$adapter->quoteValue('%'.$value.'%');

            case 'icontains':
                return 'LOWER('.$adapter->quoteColumn($column).') LIKE '.$adapter->quoteValue('%'.mb_strtolower($value, 'UTF-8').'%');

            case 'startswith':
                return $adapter->quoteColumn($column).' LIKE '.$adapter->quoteValue($value.'%');

            case 'istartswith':
                return 'LOWER('.$adapter->quoteColumn($column).') LIKE '.$adapter->quoteValue(mb_strtolower($value, 'UTF-8').'%');

            case 'endswith':
                return $adapter->quoteColumn($column).' LIKE '.$adapter->quoteValue('%'.$value);

            case 'iendswith':
                return 'LOWER('.$adapter->quoteColumn($column).') LIKE '.$adapter->quoteValue('%'.mb_strtolower($value, 'UTF-8'));

            case 'in':
                if (is_array($value)) {
                    $quotedValues = array_map(function ($item) use ($adapter) {
                        return $adapter->quoteValue($item);
                    }, $value);
                    $sqlValue = implode(', ', $quotedValues);
                } elseif ($value instanceof QueryBuilder) {
                    $sqlValue = $value->toSQL();
                } else {
                    $sqlValue = $adapter->quoteSql($value);
                }

                return $adapter->quoteColumn($column).' IN ('.$sqlValue.')';

            case 'raw':
                return $adapter->quoteColumn($column).' '.$adapter->quoteSql($value);

            default:
                return;
        }
    }
}
