<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $node */
        $node = $treeBuilder->root('core23_facebook');

        $this->addRoutingSection($node);
        $this->addApiSection($node);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRoutingSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('auth_success')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route')->defaultNull()->end()
                        ->arrayNode('route_parameters')
                            ->defaultValue([])
                            ->prototype('array')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('auth_error')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route')->defaultNull()->end()
                        ->arrayNode('route_parameters')
                            ->defaultValue([])
                            ->prototype('array')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addApiSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('api')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('app_id')->isRequired()->end()
                        ->scalarNode('app_secret')->isRequired()->end()
                        ->arrayNode('permissions')
                            ->prototype('scalar')->end()
                            ->defaultValue(['public_profile', 'user_likes'])
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;
    }
}
