<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:02
 */

namespace Mindy\QueryBuilder;

use Mindy\QueryBuilder\Interfaces\ICallback;
use Mindy\QueryBuilder\Interfaces\ILookupBuilder;

class QueryBuilderFactory
{
    /**
     * @var BaseAdapter
     */
    protected $adapter;
    /**
     * @var ILookupBuilder
     */
    protected $lookupBuilder;

    /**
     * QueryBuilder constructor.
     * @param BaseAdapter $adapter
     * @param ILookupBuilder $lookupBuilder
     * @param ICallback $callback
     */
    public function __construct(BaseAdapter $adapter, ILookupBuilder $lookupBuilder)
    {
        $this->adapter = $adapter;
        $this->lookupBuilder = $lookupBuilder;
    }

    public function getQueryBuilder()
    {
        return new QueryBuilder($this->adapter, $this->lookupBuilder);
    }
}