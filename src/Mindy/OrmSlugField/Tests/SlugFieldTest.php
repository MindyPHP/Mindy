<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Fields\Tests;

use Mindy\Orm\Tests\OrmDatabaseTestCase;

abstract class SlugFieldTest extends OrmDatabaseTestCase
{
    protected function getModels()
    {
        return [new AutoSlugModel()];
    }

    public function testCreate()
    {
        $model = new AutoSlugModel(['name' => 'test']);
        $this->assertTrue($model->save());
        $this->assertEquals('test', $model->slug);

        $model = new AutoSlugModel(['name' => 'привет мир!']);
        $this->assertTrue($model->save());
        $this->assertEquals('privet-mir', $model->slug);
    }

    public function testClear()
    {
        $model = new AutoSlugModel(['name' => 'test']);
        $this->assertTrue($model->save());
        $this->assertEquals('test', $model->slug);

        $model->name = 'Привет мир!';
        $model->slug = '';
        $this->assertTrue($model->save());
        $this->assertEquals('privet-mir', $model->slug);
    }

    public function testUpdate()
    {
        $model = new AutoSlugModel(['name' => 'test']);
        $this->assertTrue($model->save());
        $this->assertEquals('test', $model->slug);

        $model->name = 'Привет мир!';
        $this->assertTrue($model->save());
        $this->assertEquals('test', $model->slug);
    }

    public function testCreateParent()
    {
        $parent = new AutoSlugModel(['name' => 'parent']);
        $this->assertTrue($parent->save());

        $model = new AutoSlugModel(['name' => 'child', 'parent' => $parent]);
        $this->assertTrue($model->save());
        $this->assertEquals('parent/child', $model->slug);
    }

    public function testMoveFromParentToRoot()
    {
        $parent = new AutoSlugModel(['name' => 'parent']);
        $this->assertTrue($parent->save());

        $model = new AutoSlugModel(['name' => 'child', 'parent' => $parent]);
        $this->assertTrue($model->save());
        $this->assertEquals('parent/child', $model->slug);

        $model->parent = null;
        $this->assertTrue($model->save());
        $this->assertEquals('child', $model->slug);
    }

    public function testMoveFromParentToAnotherParent()
    {
        $parent1 = new AutoSlugModel(['name' => 'parent1']);
        $this->assertTrue($parent1->save());

        $parent2 = new AutoSlugModel(['name' => 'parent2']);
        $this->assertTrue($parent2->save());

        $model = new AutoSlugModel(['name' => 'child', 'parent' => $parent1]);
        $this->assertTrue($model->save());
        $this->assertEquals('parent1/child', $model->slug);

        $model->parent = $parent2;
        $this->assertTrue($model->save());
        $this->assertEquals('parent2/child', $model->slug);
    }

    public function testMoveRootToAnotherRoot()
    {
        $parent1 = new AutoSlugModel(['name' => 'parent1']);
        $this->assertTrue($parent1->save());

        $parent2 = new AutoSlugModel(['name' => 'parent2']);
        $this->assertTrue($parent2->save());

        $parent1->parent = $parent2;
        $this->assertTrue($parent1->save());
        $this->assertEquals('parent2/parent1', $parent1->slug);
    }

    public function testUpdateParent()
    {
        $parent = new AutoSlugModel(['name' => 'parent']);
        $this->assertTrue($parent->save());
        $this->assertSame('parent', $parent->slug);

        $child = new AutoSlugModel(['name' => 'child', 'parent' => $parent]);
        $this->assertTrue($child->save());
        $this->assertSame('parent/child', $child->slug);

        $parent = AutoSlugModel::objects()->get(['name' => 'parent']);
        $parent->slug = 'tools';
        $this->assertTrue($parent->save());
        $this->assertSame('tools', $parent->slug);

        /** @var \Mindy\Orm\Model $child */
        $child = AutoSlugModel::objects()->get(['name' => 'child']);
        $this->assertSame('tools/child', $child->slug);
    }
}
