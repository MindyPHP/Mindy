<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests;

use Mindy\Orm\Tests\Models\Customer;
use Mindy\Orm\Tests\Models\Order;

class NullTest extends OrmDatabaseTestCase
{
    public $driver = 'mysql';

    protected function getModels()
    {
        return [new Order(), new Customer()];
    }

    public function testIsNull()
    {
        $this->assertTrue((new Order(['customer_id' => 1]))->save());
        $this->assertTrue((new Order(['customer_id' => 1, 'discount' => 1]))->save());

        $this->assertEquals(2, Order::objects()->count());

        $o1 = Order::objects()->get(['pk' => 1]);
        $this->assertNull($o1->discount);

        $o2 = Order::objects()->get(['pk' => 2]);
        $this->assertNotNull($o2->discount);

        $this->assertEquals(1, Order::objects()->filter(['discount__isnull' => true])->count());
        $this->assertEquals(1, Order::objects()->filter(['discount__isnull' => false])->count());
    }
}
