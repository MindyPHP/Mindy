<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/06/16
 * Time: 15:35
 */

namespace Mindy\QueryBuilder\LookupBuilder;

use Mindy\QueryBuilder\Interfaces\ICallback;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;
use Mindy\QueryBuilder\QueryBuilder;

abstract class Base implements ILookupBuilder
{
    /**
     * @var string
     */
    protected $default = 'exact';
    /**
     * @var string
     */
    protected $separator = '__';
    /**
     * @var array
     */
    protected $lookups = [];
    /**
     * @var callable|null
     */
    protected $callback = null;
    /**
     * @var null
     */
    protected $qb = null;

    public function __construct(array $lookups, ICallback $callback = null)
    {
        $this->lookups = $lookups;
        $this->callback = $callback;
    }

    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function hasLookup($lookup)
    {
        return array_key_exists($lookup, $this->lookups);
    }

    abstract public function parse(array $where);
}