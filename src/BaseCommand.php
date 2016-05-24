<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Factory\EntityManagerFactory;
use Mindgruve\Gruver\Factory\LoggerFactory;
use Mindgruve\Gruver\Factory\UrlFactory;
use Mindgruve\Gruver\Helper\HAProxyHelper;
use Mindgruve\Gruver\Process\DockerComposeProcess;
use Mindgruve\Gruver\Process\DockerProcess;
use Mindgruve\Gruver\Process\Sqlite3Process;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
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
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $container = $this->container;
        $config = $container['config'];

        $projectName = $config->get('[project][name]');
        $services = $config->get('[project][public_services]');
        $serviceName = null;
        $tag = null;

        if ($input->hasOption('project_name')) {
            $input->setOption('project_name', $projectName);
        }

        if ($input->hasOption('service_name')) {
            $serviceName = $input->getOption('service_name');
            if (!$serviceName) {
                if (count($services) == 1) {
                    $serviceName = $services[0]['name'];
                } else {
                    $serviceNames = array();
                    foreach ($services as $service) {
                        $serviceNames[] = $service['name'];
                    }
                    $question = new ChoiceQuestion(
                        $this->questionServiceName,
                        $serviceNames
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
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = new Container();
        $container['config'] = new GruverConfig();
        $container['dispatcher'] = function ($c) use ($output) {
            return new EventDispatcher($c['config'], $output);
        };

        $twigPaths = array();
        if (file_exists('/etc/gruver/templates')) {
            $twigPaths[] = '/etc/gruver/templates';
        }

        if (file_exists(__DIR__.'/Resources/templates')) {
            $twigPaths[] = __DIR__.'/Resources/templates';
        }
        $container['twig_paths'] = $twigPaths;

        $container['twig'] = function ($c) {
            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem(
                $c['twig_paths']
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
            return new HAProxyHelper($c['twig'], $c['entity_manager'], $c['config']);
        };
        $container['entity_manger.factory'] = function ($c) {
            return new EntityManagerFactory($c['config']);
        };
        $container['entity_manager'] = function ($c) {
            return $c['entity_manger.factory']->getEntityManager();
        };
        $container['db_params'] = function ($c) {
            return $c['entity_manger.factory']->getDatabaseParams();
        };
        $container['file_system'] = function ($c) {
            return new Filesystem();
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
     * @param int $timeout
     * @param OutputInterface $output
     * @return Process
     */
    protected function runProcess($cmd, $timeout = 3600, OutputInterface $output = null)
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
     * @param int $timeout
     * @param OutputInterface $output
     * @return Process
     */
    protected function mustRunProcess($cmd, $timeout = 3600, OutputInterface $output = null)
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

    protected function checkConfigHash(Project $project, InputInterface $input, OutputInterface $output)
    {
        $config = $this->get('config');
        $helper = $this->getHelper('question');

        if ($project->getConfigHash() != $config->getConfigHash()) {
            $question = new ConfirmationQuestion(
                'Load new gruver config? <info>(y/n)</info>  ',
                false
            );
            $loadConfig = $helper->ask($input, $output, $question);

            if ($loadConfig) {
                $command = $this->getApplication()->find('load-config');
                $code = $command->run(new ArrayInput(array()), $output);
                if(!$code){
                    exit;
                }
            }
        }
    }
}
