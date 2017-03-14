<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\MenuBundle\Tests\Form\Admin;

use Doctrine\DBAL\DriverManager;
use Mindy\Bundle\FormBundle\Form\Extension\HelpExtension;
use Mindy\Bundle\MenuBundle\Form\Admin\MenuForm;
use Mindy\Bundle\MenuBundle\Model\Menu;
use Mindy\Orm\Orm;
use Mindy\Orm\Sync;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ValidatorBuilder;

class MenuFormTest extends TypeTestCase
{
    private $validator;

    protected function getTypedExtensions()
    {
        return [
            new HelpExtension($this->validator),
        ];
    }

    protected function getExtensions()
    {
        $this->validator = (new ValidatorBuilder())
            ->getValidator();

        return [
            new ValidatorExtension($this->validator),
        ];
    }

    public function formDataProvider()
    {
        return [
            [
                [],
                [
                    'name' => ['This value should not be blank.'],
                ],
            ],
            [
                ['slug' => '!!!'],
                [
                    'name' => ['This value should not be blank.'],
                    'slug' => ['The string "!!!" contains an illegal character: it can only contain letters or numbers.'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider formDataProvider
     */
    public function testMenuForm($formData, $formErrors)
    {
        $connection = DriverManager::getConnection([
            'memory' => 'true',
            'driver' => 'pdo_sqlite',
        ]);
        Orm::setDefaultConnection($connection);

        $sync = new Sync([new Menu()], $connection);
        $sync->create();

        $menu = new Menu();
        $form = $this->factory->create(MenuForm::class, $menu);

        $form->submit($formData);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($menu, $form->getData());
        $this->assertFalse($menu->isValid());
        $this->assertEquals($formErrors, $menu->getErrors());
    }
}
