<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 20:26.
 */

namespace Mindy\Bundle\TableBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TablePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('table.registry')) {
            return;
        }

        $definition = $container->getDefinition('table.registry');

        // Builds an array with fully-qualified type class names as keys and service IDs as values
        $types = array();
        foreach ($container->findTaggedServiceIds('table.table') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as form types are lazy-loaded.', $serviceId));
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(0, $types);

        // Builds an array with fully-qualified type class names as keys and service IDs as values
        $columns = array();
        foreach ($container->findTaggedServiceIds('table.column') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as form types are lazy-loaded.', $serviceId));
            }

            // Support type access by FQCN
            $columns[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(1, $columns);
    }
}
