<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Fields\EmailField;
use PHPUnit\Framework\TestCase;

class EmailFieldTest extends TestCase
{
    public function testEmail()
    {
        $field = new EmailField();

        $field->setValue('foo@bar.com');
        $this->assertTrue($field->isValid());

        $field->setValue('123');
        $this->assertFalse($field->isValid());
    }
}
