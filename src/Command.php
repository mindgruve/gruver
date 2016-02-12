<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Factory\EntityManagerFactory;
use Mindgruve\Gruver\Factory\LoggerFactory;
use Mindgruve\Gruver\Factory\UrlFactory;
use Mindgruve\Gruver\Process\DockerComposeProcess;
use Mindgruve\Gruver\Process\DockerProcess;
use Mindgruve\Gruver\Process\Sqlite3Process;
use Pimple\Container;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Command extends BaseCommand
{
    /**
     * @var Container
     */
    protected $container;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $gruverYaml = $input->hasOption('gruver_file') ? $input->getOption('gruver_file') : null;

        $container = new Container();
        $container['config'] = function () use ($gruverYaml) {
            return new GruverConfig($gruverYaml);
        };
        $container['dispatcher'] = function ($c) use ($output) {
            return new EventDispatcher($c['config'], $output);
        };
        $container['docker_compose'] = function ($c) {
            return new DockerComposeProcess($c['config']);
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
        $container['entity.manager'] = function ($c) {
            $factory = new EntityManagerFactory($c['config']);

            return $factory->getEntityManager();
        };

        $this->container = $container;

        parent::initialize($input, $output);
    }

    protected function runProcess($cmd, GruverConfig $config, $timeout = 3600, OutputInterface $output = null)
    {
        $cmd = $config->getEnvironmentalVariableExport().' '.$cmd;

        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->run(
            function ($type, $buffer) use ($output) {
                if ($output) {
                    $output->write($buffer);
                }
            }
        );
    }

    protected function mustRunProcess($cmd, GruverConfig $config, $timeout = 3600, OutputInterface $output = null)
    {
        $cmd = $config->getEnvironmentalVariableExport().' '.$cmd;

        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->mustRun(
            function ($type, $buffer) use ($output) {
                if ($output) {
                    $output->write($buffer);
                }
            }
        );
    }
}
