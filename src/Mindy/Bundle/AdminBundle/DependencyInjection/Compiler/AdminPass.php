<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('admin.registry')) {
            return;
        }

        $definition = $container->getDefinition('admin.registry');
        if ($definition) {
            foreach ($container->findTaggedServiceIds('admin.admin') as $id => $params) {
                $adminDefinition = $container->getDefinition($id);

                $adminDefinition
                    ->addMethodCall('setContainer', [new Reference('service_container')]);

                $attributes = array_shift($params);
                if (isset($attributes['slug'])) {
                    $adminDefinition
                        ->addMethodCall('setAdminId', [$attributes['slug']]);

                    $definition->addMethodCall('addAdmin', [
                        $id,
                        $attributes['slug'],
                    ]);
                }
            }
        }
    }
}
