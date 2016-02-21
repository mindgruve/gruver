<?php

namespace Mindgruve\Gruver\Factory;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Helper\HAProxyHelper;
use Mindgruve\Gruver\Process\DockerProcess;
use Mindgruve\Gruver\Process\Sqlite3Process;
use Pimple\Container;
use Symfony\Component\Console\Output\Output;
use Mindgruve\Gruver\EventDispatcher;

class ServiceContainerFactory
{

    public static function build(EnvironmentalVariables $envVar, Output $output){
        $container = new Container();
        $container['config'] = new GruverConfig();
        $container['env_vars'] = $envVar;
        $container['dispatcher'] = function ($c) use ($output) {
            return new EventDispatcher($c['config'], $output);
        };
        $container['twig'] = function ($c) {
            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem(
                array(
                    '/etc/gruver/templates',
                    __DIR__ . '/Resources/templates'
                )
            );

            return new \Twig_Environment($loader);
        };
        $container['docker'] = function ($c) {
            return new DockerProcess($c['config']);
        };
        $container['sqlite3'] = function ($c) {
            return new Sqlite3Process($c['config']);
        };
        $container['logger.factory'] = function ($c) use ($output) {
            return new LoggerFactory($c['config'], $output);
        };
        $container['logger'] = function ($c) {
            return $c['logger.factory']->getLogger();
        };
        $container['url.factory'] = function ($c) {
            return new UrlFactory($c['config']);
        };
        $container['haproxy.helper'] = function ($c) {
            return new HAProxyHelper($c['twig'], $c['entity_manager']);
        };
        $container['entity_manager'] = function ($c) {
            $factory = new EntityManagerFactory($c['config']);

            return $factory->getEntityManager();
        };

        return $container;
    }


}