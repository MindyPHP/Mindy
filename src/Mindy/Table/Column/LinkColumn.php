<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:30.
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
