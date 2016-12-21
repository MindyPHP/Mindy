<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:05.
 */

namespace Mindy\Component\Table\Column;

use Symfony\Component\PropertyAccess\PropertyAccessor;

interface ColumnInterface
{
    public function setPropertyAccessor(PropertyAccessor $accessor);

    public function setOptions(array $options);
}
