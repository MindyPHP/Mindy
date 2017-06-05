<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\TemplateBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged routing.loader services to routing.resolver service.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TemplatePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('templating.engine.mindy')) {
            return;
        }

        $definitionChain = $container->getDefinition('template.finder.chain');
        if ($definitionChain) {
            foreach ($container->findTaggedServiceIds('template.finder') as $id => $attributes) {
                $definitionChain->addMethodCall('addFinder', [new Reference($id)]);
            }
        }

        $definition = $container->getDefinition('templating.engine.mindy');
        if ($definition) {
            foreach ($container->findTaggedServiceIds('template.library') as $id => $attributes) {
                $definition->addMethodCall('addLibrary', [new Reference($id)]);
            }

            foreach ($container->findTaggedServiceIds('template.helper') as $id => $attributes) {
                $definition->addMethodCall('addHelper', [key($attributes), current($attributes)]);
            }

            foreach ($container->findTaggedServiceIds('template.variable_provider') as $id => $attributes) {
                $definition->addMethodCall('addVariableProvider', [new Reference($id)]);
            }
        }
    }
}
