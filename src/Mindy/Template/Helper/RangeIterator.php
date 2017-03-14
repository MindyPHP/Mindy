<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template\Helper;

use Iterator;

/**
 * Class RangeIterator.
 */
class RangeIterator implements Iterator
{
    protected $lower;
    protected $upper;
    protected $step;
    protected $current;

    public function __construct($lower, $upper, $step = 1)
    {
        $this->lower = $lower;
        $this->upper = $upper;
        $this->step = $step;
    }

    public function length()
    {
        return \abs($this->upper - $this->lower) / \abs($this->step);
    }

    public function includes($n)
    {
        if ($this->upper >= $this->lower) {
            return $n >= $this->lower && $n <= $this->upper;
        }

        return $n <= $this->lower && $n >= $this->upper;
    }

    public function random($seed = null)
    {
        if (isset($seed)) {
            mt_srand($seed);
        }

        return $this->upper >= $this->lower ?
            mt_rand($this->lower, $this->upper) :
            mt_rand($this->upper, $this->lower);
    }

    public function rewind()
    {
        $this->current = $this->lower;
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        if ($this->upper >= $this->lower) {
            return $this->current >= $this->lower && $this->current <= $this->upper;
        }

        return $this->current <= $this->lower && $this->current >= $this->upper;
    }

    public function next()
    {
        $this->current += $this->step;

        return $this;
    }

    public function current()
    {
        return $this->current;
    }
}
