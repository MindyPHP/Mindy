<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests\Databases\Mysql\Fields;

use Mindy\Orm\Tests\Fields\HasManyFieldTest;

class MysqlHasManyFieldTest extends HasManyFieldTest
{
    public $driver = 'mysql';
}
