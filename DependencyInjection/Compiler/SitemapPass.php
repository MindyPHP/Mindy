<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12/12/2016
 * Time: 00:38
 */

namespace Mindy\Bundle\SitemapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SitemapPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false == $container->has('sitemap.builder')) {
            return;
        }

        $definition = $container->getDefinition('sitemap.builder');
        foreach ($container->findTaggedServiceIds('sitemap.provider') as $id => $params) {
            $definition->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}