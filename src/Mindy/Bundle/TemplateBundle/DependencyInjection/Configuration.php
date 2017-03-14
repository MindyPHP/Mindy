<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\TemplateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('template');

        $rootNode
            ->children()
                ->scalarNode('mode')->defaultValue(0)->end()
                ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%/templates')->end()
                ->booleanNode('auto_escape')->defaultTrue()->end()
        ;

        $this->addThemeSection($rootNode);
        $this->addBundlesSection($rootNode);
        $this->addTemplatesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    private function addThemeSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('theme')->info('theme templates configuration')->canBeEnabled()
                    ->children()
                        ->variableNode('basePath')->defaultValue('%kernel.root_dir%/Resources')->end()
                        ->variableNode('theme')->defaultValue('default')->end()
                        ->variableNode('templatesDir')->defaultValue('templates')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    private function addBundlesSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('bundles')
                    ->info('bundles templates configuration')
                    ->canBeDisabled()
                        ->children()
                            ->variableNode('templatesDir')->defaultValue('templates')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    private function addTemplatesSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('templates')
                    ->info('templates configuration')
                    ->canBeDisabled()
                        ->children()
                            ->variableNode('basePath')->defaultValue('%kernel.root_dir%/Resources')->end()
                            ->variableNode('templatesDir')->defaultValue('templates')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
