<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 12:46.
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Fields\IpField;

class IpFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testIpV4()
    {
        $field = new IpField();

        $field->setValue('foo');
        $this->assertFalse($field->isValid());

        $field->setValue('127.0.0.1');
        $this->assertTrue($field->isValid());

        $field->setValue('127.0.0');
        $this->assertFalse($field->isValid());

        $field->setValue('127.0.0.1.1');
        $this->assertFalse($field->isValid());
    }

    public function testIpV6()
    {
        $field = new IpField([
            'version' => 6,
        ]);

        $field->setValue('0:0:0:0:0:0:0');
        $this->assertFalse($field->isValid());

        $field->setValue('0:0:0:0:0:0:0:1');
        $this->assertTrue($field->isValid());
    }

    /**
     * @expectedException \LogicException
     */
    public function testIpWrong()
    {
        $field = new IpField([
            'version' => 8,
        ]);
    }
}
