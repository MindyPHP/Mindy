<?php

/*
 * This file is part of SentryBundle.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SentryBundle\Test;

use Mindy\Bundle\SentryBundle\ErrorTypesParser;

class ErrorTypesParserTest extends \PHPUnit_Framework_TestCase
{
    public function test_error_types_parser()
    {
        $ex = new ErrorTypesParser('E_ALL & ~E_DEPRECATED & ~E_NOTICE');
        $this->assertEquals($ex->parse(), E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    }
}
