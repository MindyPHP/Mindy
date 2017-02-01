<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
