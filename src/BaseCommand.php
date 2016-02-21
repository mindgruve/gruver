<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Factory\ServiceContainerFactory;
use Mindgruve\Gruver\Process\DockerComposeProcess;
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
        $this->container = ServiceContainerFactory::build($this->envVar, $output);
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
