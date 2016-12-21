<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 12:51.
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Fields\PositionField;
use Mindy\Orm\ModelInterface;

class PositionFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testPositionField()
    {
        $i = 0;
        $callback = function () use ($i) {
            global $i;
            $i += 1;

            return $i;
        };

        $field = new PositionField([
            'callback' => $callback,
        ]);

        $model = $this
            ->getMockBuilder(ModelInterface::class)
            ->getMock();

        $this->assertInstanceOf(ModelInterface::class, $model);
        $this->assertEquals(1, $field->getNextPosition($model));
        $this->assertEquals(2, $field->getNextPosition($model));
        $this->assertEquals(3, $field->getNextPosition($model));
    }
}
