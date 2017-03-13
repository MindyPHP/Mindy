<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Fields\JsonField;

class JsonFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testEncodeDecode()
    {
        $field = new JsonField();
        $field->setValue(['1' => 1]);
        $this->assertTrue(is_array($field->getValue()));
        $this->assertEquals(['1' => 1], $field->getValue());
        $this->assertEquals(1, $field->getValue()['1']);
    }

    public function testValidation()
    {
        $field = new JsonField();
        $field->setValue(new \stdClass());
        $this->assertFalse($field->isValid());
    }
}
