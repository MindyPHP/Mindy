<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28/11/16
 * Time: 10:09.
 */

namespace Mindy\Component\Table\Column;

class ActionsColumn extends AbstractColumn
{
    protected $actions = [];

    public function getValue($row)
    {
        return implode(', ', $this->actions);
    }
}
