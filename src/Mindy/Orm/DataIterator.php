<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm;

use ArrayIterator;

/**
 * Class DataIterator.
 */
class DataIterator extends ArrayIterator
{
    /**
     * @var bool
     */
    public $asArray;
    /**
     * @var QuerySet
     */
    public $qs;

    /**
     * DataIterator constructor.
     *
     * @param array $data
     * @param array $config
     * @param int   $flags
     */
    public function __construct(array $data, array $config = [], $flags = 0)
    {
        parent::__construct($data, $flags);

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
