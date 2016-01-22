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
                        ->scalarNode('name')->isRequired()->end()
                        ->arrayNode('email_notifications')
                            ->prototype('scalar')->end()
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
                    ->end()
                ->end()
                ->arrayNode('config')
                    ->children()
                        ->scalarNode('automatic_deployment')->end()
                        ->scalarNode('docker_binary')->end()
                        ->scalarNode('docker_compose_binary')->end()
                        ->arrayNode('health_checks')
                            ->prototype('scalar')->end()
                        ->end()
                        ->booleanNode('remove_exited_containers')->end()
                        ->booleanNode('remove_orphan_images')->end()
                    ->end()
                ->end()
                ->arrayNode('events')
                    ->children()
                        ->arrayNode('pre_build')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('post_build')
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
                        ->arrayNode('pre_cleanup')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('post_cleanup')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('pre_status')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('post_status')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}