<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Database\Pgsql;

use Mindy\QueryBuilder\BaseLookupCollection;
use Mindy\QueryBuilder\Interfaces\IAdapter;

class LookupCollection extends BaseLookupCollection
{
    public function has($lookup)
    {
        $lookups = [
            'regex', 'iregex', 'second', 'year', 'minute',
            'hour', 'day', 'month', 'week_day',
        ];
        if (in_array($lookup, $lookups)) {
            return true;
        }

        return parent::has($lookup);
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
            case 'second':
                return 'EXTRACT(SECOND FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'year':
                return 'EXTRACT(YEAR FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'minute':
                return 'EXTRACT(MINUTE FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'hour':
                return 'EXTRACT(HOUR FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'day':
                return 'EXTRACT(DAY FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'month':
                return 'EXTRACT(MONTH FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'week_day':
                return 'EXTRACT(DOW FROM '.$adapter->quoteColumn($column).'::timestamp)='.$adapter->quoteValue((string) $value);

            case 'regex':
                return $adapter->quoteColumn($column).'~'.$adapter->quoteValue($value);

            case 'iregex':
                return $adapter->quoteColumn($column).'~*'.$adapter->quoteValue($value);

            case 'contains':
                return $adapter->quoteColumn($column).'::text LIKE '.$adapter->quoteValue('%'.$value.'%');

            case 'icontains':
                return 'LOWER('.$adapter->quoteColumn($column).'::text) LIKE '.$adapter->quoteValue('%'.mb_strtolower($value, 'UTF-8').'%');

            case 'startswith':
                return $adapter->quoteColumn($column).'::text LIKE '.$adapter->quoteValue($value.'%');

            case 'istartswith':
                return 'LOWER('.$adapter->quoteColumn($column).'::text) LIKE '.$adapter->quoteValue(mb_strtolower($value, 'UTF-8').'%');

            case 'endswith':
                return $adapter->quoteColumn($column).'::text LIKE '.$adapter->quoteValue('%'.$value);

            case 'iendswith':
                return 'LOWER('.$adapter->quoteColumn($column).'::text) LIKE '.$adapter->quoteValue('%'.mb_strtolower($value, 'UTF-8'));
        }

        return parent::process($adapter, $lookup, $column, $value);
    }
}
