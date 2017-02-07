<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/02/2017
 * Time: 20:50
 */

namespace Mindy\Bundle\SeoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SeoPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('mindy.bundle.seo.registry')) {
            return;
        }

        $definition = $container->getDefinition('mindy.bundle.seo.registry');
        foreach ($container->findTaggedServiceIds('seo.generator') as $id => $parameters) {
            $definition->addMethodCall('addGenerator', [new Reference($id)]);
        }
    }
}