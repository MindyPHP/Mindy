<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Aggregation;

use Mindy\QueryBuilder\Expression;

class Aggregation extends Expression
{
    protected $alias;

    protected $tableAlias;

    protected $fields;

    protected $fieldsSql = '';

    public function setFieldsSql($sql)
    {
        $this->fieldsSql = $sql;

        return $this;
    }

    public function setTableAlias($alias)
    {
        $this->tableAlias = $alias;

        return $this;
    }

    public function toSQL()
    {
        return (empty($this->tableAlias) ? '' : '[['.$this->tableAlias.']].').$this->fieldsSql;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function __construct($field, $alias = '')
    {
        $this->fields = $field;
        $this->alias = $alias;
    }
}
