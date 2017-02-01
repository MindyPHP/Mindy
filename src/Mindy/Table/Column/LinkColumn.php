<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Component\Table\Column;

class LinkColumn extends TextColumn
{
    protected $router;

    protected $generateUrl;

    public function getValue($row)
    {
        $value = parent::getValue($row);

        if (is_callable($this->generateUrl)) {
            $url = $this->generateUrl->__invoke($row);
        } else {
            $url = '#';
        }

        return sprintf('<a href="%s">%s</a>', $url, $value);
    }
}
