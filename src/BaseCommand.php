<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Factory\EntityManagerFactory;
use Mindgruve\Gruver\Factory\LoggerFactory;
use Mindgruve\Gruver\Factory\UrlFactory;
use Mindgruve\Gruver\Process\DockerComposeProcess;
use Mindgruve\Gruver\Process\DockerProcess;
use Mindgruve\Gruver\Process\Sqlite3Process;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class BaseCommand extends Command
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var GruverConfig
     */
    protected $config;

    /**
     * @var EnvironmentalVariables
     */
    protected $envVar;

    /**
     * @var string
     */
    protected $questionServiceName = '';

    /**
     * @var string
     */
    protected $questionTag = '';

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $container = $this->container;
        $config = $container['config'];

        $projectName = $config->get('[project][name]');
        $services = $config->get('[project][services]');
        $serviceName = null;
        $tag = null;

        if ($input->hasOption('service_name')) {
            $serviceName = $input->getOption('service_name');
            if (!$serviceName) {
                if (count($services) == 1) {
                    $serviceName = $services[0];
                } else {
                    $question = new ChoiceQuestion(
                        $this->questionServiceName,
                        $services
                    );
                    $serviceName = $helper->ask($input, $output, $question);
                }

                $input->setOption('service_name', $serviceName);
            }
        }

        if ($input->hasOption('tag')) {
            $tag = $input->getOption('tag');
            if (!$tag) {
                $question = new Question($this->questionTag);
                $tag = $helper->ask($input, $output, $question);
                $input->setOption('tag', $tag);
            }
        }

        $container['env_vars'] = new EnvironmentalVariables($config, $projectName, $serviceName, $tag);
        $container['docker_compose'] = function ($c) {
            return new DockerComposeProcess($c['config'], $c['env_vars'], $c['twig']);
        };
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = new Container();
        $container['config'] = new GruverConfig();
        $container['env_vars'] = $this->envVar;
        $container['dispatcher'] = function ($c) use ($output) {
            return new EventDispatcher($c['config'], $output);
        };
        $container['twig'] = function ($c) {
            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem(__DIR__.'/Resources/templates');

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
        $container['entity_manager'] = function ($c) {
            $factory = new EntityManagerFactory($c['config']);

            return $factory->getEntityManager();
        };

        $this->container = $container;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $serviceKey
     *
     * @return mixed
     */
    protected function get($serviceKey)
    {
        $container = $this->getContainer();

        return $container[$serviceKey];
    }

    /**
     * @param $cmd
     * @param GruverConfig $config
     * @param int $timeout
     * @param OutputInterface $output
     * @return Process
     */
    protected function runProcess($cmd, GruverConfig $config, $timeout = 3600, OutputInterface $output = null)
    {
        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->run(
            function ($type, $buffer) use ($output) {
                if ($output) {
                    $output->write($buffer);
                }
            }
        );

        return $process;
    }

    /**
     * @param $cmd
     * @param GruverConfig $config
     * @param int $timeout
     * @param OutputInterface $output
     * @return Process
     */
    protected function mustRunProcess($cmd, GruverConfig $config, $timeout = 3600, OutputInterface $output = null)
    {
        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->mustRun(
            function ($type, $buffer) use ($output) {
                if ($output) {
                    $output->write($buffer);
                }
            }
        );

        return $process;
    }
}
