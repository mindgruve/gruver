<?php

namespace Mindgruve\Gruver\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class GruverConfigSchema implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gruver');
        $rootNode
            ->children()
                ->arrayNode('application')
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('email_notifications')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('notifications')
                    ->children()
                        ->arrayNode('email')
                            ->children()
                                ->arrayNode('recipients')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('build')
                    ->children()
                        ->enumNode('method')
                            ->values(array('docker_compose', 'docker_repository'))
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('staging')
                    ->children()
                        ->scalarNode('url_pattern')->end()
                        ->arrayNode('allowed_ips')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('production')
                    ->children()
                        ->arrayNode('hosts')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('notifications_email')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('binaries')
                    ->children()
                        ->scalarNode('docker_compose')->end()
                    ->end()
                ->end()
                ->arrayNode('health_checks')->end()
                ->booleanNode('automatic_deployment')
                    ->defaultFalse()
                ->end()
                ->arrayNode('events')
                    ->children()
                        ->arrayNode('pre_build')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('post_build')
                            ->prototype('scalar')->end()
                            ->end()
                        ->arrayNode('pre_pull')
                            ->prototype('scalar')->end()
                            ->end()
                        ->arrayNode('post_pull')
                            ->prototype('scalar')->end()
                            ->end()
                        ->arrayNode('pre_promote')
                            ->prototype('scalar')->end()
                            ->end()
                        ->arrayNode('post_promote')
                            ->prototype('scalar')->end()
                            ->end()
                        ->arrayNode('pre_rollback')
                            ->prototype('scalar')->end()
                            ->end()
                        ->arrayNode('post_rollback')
                            ->prototype('scalar')->end()
                            ->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}