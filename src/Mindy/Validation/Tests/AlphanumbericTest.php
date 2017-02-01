<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
