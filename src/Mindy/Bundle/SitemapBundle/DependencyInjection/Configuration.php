<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 09/02/2017
 * Time: 22:15
 */

namespace Mindy\Bundle\SitemapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sitemap');

        $rootNode
            ->children()
                ->scalarNode('scheme')
                    ->defaultValue('http')
                    ->isRequired()
                    ->validate()
                    ->ifNotInArray(['http', 'https'])
                        ->thenInvalid('Invalid http schema: %s')
                    ->end()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('host')
                    ->defaultValue('example.com')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('save_path')
                    ->defaultValue('%kernel.root_dir%/../web')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
