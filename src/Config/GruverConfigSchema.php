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
                ->arrayNode('project')
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('public_services')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->arrayNode('hosts')
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->arrayNode('ports')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('email_notifications')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('binaries')
                    ->children()
                        ->scalarNode('docker_binary')->end()
                        ->scalarNode('docker_compose_binary')->end()
                        ->scalarNode('sqlite3_binary')->end()
                    ->end()
                ->end()
                ->arrayNode('directories')
                    ->children()
                        ->scalarNode('config_dir')->end()
                        ->scalarNode('cache_dir')->end()
                        ->scalarNode('data_dir')->end()
                        ->scalarNode('templates_dir')->end()
                        ->scalarNode('migrations_dir')->end()
                        ->scalarNode('releases_dir')->end()
                        ->scalarNode('logging_dir')->end()
                    ->end()
                ->end()
                ->arrayNode('database')
                    ->children()
                        ->scalarNode('driver')->end()
                        ->scalarNode('user')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('path')->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->children()
                        ->arrayNode('adapters')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('channel')->end()
                                    ->scalarNode('type')->end()
                                    ->scalarNode('path')->end()
                                    ->scalarNode('level')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('config')
                    ->children()
                        ->scalarNode('dev_mode')->end()
                        ->scalarNode('automatic_deployment')->end()
                        ->scalarNode('date_format')->end()
                        ->arrayNode('docker_compose_files')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('email_notifications')
                            ->prototype('scalar')->end()
                        ->end()
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
                        ->arrayNode('pre_run')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('post_run')
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
