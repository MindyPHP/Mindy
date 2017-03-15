<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Database\Sqlite;

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
            case 'regex':
                return $adapter->quoteColumn($column).' REGEXP '.$adapter->quoteValue('/'.$value.'/');

            case 'iregex':
                return $adapter->quoteColumn($column).' REGEXP '.$adapter->quoteValue('/'.$value.'/i');

            case 'second':
                return "strftime('%S', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'year':
                return "strftime('%Y', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'minute':
                return "strftime('%M', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'hour':
                return "strftime('%H', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'day':
                return "strftime('%d', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'month':
                $value = (int) $value;
                if (strlen($value) == 1) {
                    $value = '0'.(string) $value;
                }

                return "strftime('%m', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'week_day':
                $value = (int) $value + 1;
                if ($value == 7) {
                    $value = 1;
                }

                return "strftime('%w', ".$adapter->quoteColumn($column).')='.$adapter->quoteValue((string) $value);

            case 'range':
                list($min, $max) = $value;

                return $adapter->quoteColumn($column).' BETWEEN '.(int) $min.' AND '.(int) $max;
        }

        return parent::process($adapter, $lookup, $column, $value);
    }
}
