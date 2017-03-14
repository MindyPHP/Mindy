<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\OrmBundle\Tests\DependencyInjection;

use Mindy\Bundle\OrmBundle\DependencyInjection\OrmExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testServices()
    {
        $services = [
            'orm.connection_manager',
            'orm.execute_command',
            'orm.generate_command',
            'orm.latest_command',
            'orm.migrate_command',
            'orm.status_command',
            'orm.version_command',
        ];
        foreach ($services as $id) {
            $this->assertTrue($this->getContainer()->has($id));
        }

        $parameters = [
            'orm.file.filesystem',
            'orm.connections',
        ];
        foreach ($parameters as $id) {
            $this->assertTrue($this->getContainer()->hasParameter($id));
        }
    }

    private function getContainer(array $options = [])
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.root_dir', 'kernel/root');
        $containerBuilder->setParameter('kernel.environment', 'test');

        (new OrmExtension())->load($options, $containerBuilder);

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
