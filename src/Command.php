<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Entity\Service;
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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
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
        $config = new GruverConfig($gruverYaml);

        $container['config'] = $config;
        $container['dispatcher'] = function ($c) use ($output) {
            return new EventDispatcher($c['config'], $output);
        };
        $container['docker_compose'] = function ($c) {
            return new DockerComposeProcess($c['config'], $c['env_vars'], $c['twig']);
        };
        $container['twig'] = function ($c) {
            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem(__DIR__ . '/Resources/templates');

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

        $helper = $this->getHelper('question');

        $projectName = null;
        if ($input->hasOption('project_name')) {
            $projectName = $input->getOption('project_name') ?
                $input->getOption('project_name') :
                $config->get('[project][name]');
            $input->setOption('project_name', $projectName);
        }

        $serviceName = null;
        if ($input->hasOption('service_name')) {
            if (!$serviceName) {
                $question = new Question('What service do you want bring up?  ');
                $serviceName = $helper->ask($input, $output, $question);
            }

            $em = $container['entity_manager'];
            $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
            $service = $serviceRepository->findOneBy(array('name' => $serviceName));

            if (!$service) {

                $question = new ConfirmationQuestion(
                    'Service <info>' . $serviceName . '</info> not found.  Do you want to create it? (<info>y/n</info>)  '
                );
                $createNew = $helper->ask($input, $output, $question);
                if ($createNew) {
                    $service = new Service();
                    $service->setName($serviceName);
                    $em->persist($service);
                    $em->flush($service);
                } else {
                    $output->writeln('<error>Service not found - ' . $serviceName . '</error>');
                    exit;
                }
            }
            $input->setOption('service_name', $serviceName);
        }

        $tag = null;
        if ($input->hasOption('tag')) {
            if (!$input->getOption('tag')) {
                $question = new Question('What do you want to tag this release?  ');
                $tag = $helper->ask($input, $output, $question);
            }
            $input->setOption('tag', $tag);
        }


        $container['env_vars'] = function ($c) use ($projectName, $serviceName, $tag) {
            return new EnvironmentalVariables($c['config'], $projectName, $serviceName, $tag);
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

    protected function get($serviceKey)
    {
        $container = $this->getContainer();

        return $container[$serviceKey];
    }

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
    }

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
    }
}
