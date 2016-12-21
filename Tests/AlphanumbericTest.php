<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 14:52.
 */

namespace Mindy\Validation\Tests;

use Mindy\Validation\Alphanumeric;
use Symfony\Component\Validator\Validation;

class AlphanumbericTest extends \PHPUnit_Framework_TestCase
{
    public function testAlphanumeric()
    {
        $constraints = [new Alphanumeric()];

        $v = Validation::createValidatorBuilder()->getValidator();

        $this->assertTrue(count($v->validate('123qwe', $constraints)) === 0);
        $this->assertFalse(count($v->validate('@_-', $constraints)) === 0);
        $this->assertFalse(count($v->validate('привет мир', $constraints)) === 0);
    }
}
