<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 12:43.
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Fields\EmailField;

class EmailFieldTest extends \PHPUnit_Framework_TestCase
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
