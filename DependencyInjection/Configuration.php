<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FormBundle\DependencyInjection;

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

        $root = $treeBuilder->root('form');
        $root
            ->children()
                ->arrayNode('themes')
                    ->fixXmlConfig('themes')
                    ->addDefaultChildrenIfNoneSet()
                    ->prototype('scalar')->defaultValue('default')->end()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !in_array('default', $v);
                        })
                        ->then(function ($v) {
                            return array_merge(['default'], $v);
                        })
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
