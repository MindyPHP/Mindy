<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28/11/16
 * Time: 10:02.
 */

namespace Mindy\Component\Table\Column;

class SelectionColumn extends AbstractColumn
{
    public function getLabel()
    {
        return sprintf(
            '<input type="checkbox" name="%s" value="_all"/>',
            $this->name
        );
    }

    public function getValue($row)
    {
        $value = parent::getValue($row);

        return sprintf(
            '<input type="checkbox" name="%s" value="%s"/>',
            $this->name, $value
        );
    }
}
