<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class RunCommand extends Command
{

    const COMMAND = 'run';
    const DESCRIPTION = 'Run a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new GruverConfig();
        $env = new EnvironmentalVariables($config);
        $eventDispatcher = new EventDispatcher($config, $output);

        $output->writeln('<info>GRUVER: Running container for '.$config->getApplicationName().'</info>');

        try {
            $eventDispatcher->dispatchPreRun();

            /**
             * @todo Check that external links are running
             */

            /**
             * @todo take the application name from the command argument instead
             */

            $cmd = $env->buildExport().' '
                .$config->get('[config][docker_compose_binary]')
                .' run -d '
                .$config->getApplicationName();

            $process = new Process($cmd);
            $process->setTimeout(3600);
            $process->mustRun(
                function ($type, $buffer) use ($output) {
                    $output->write($buffer);
                }
            );
            $eventDispatcher->dispatchPostRun();
        } catch (\Exception $e) {
            $output->write('<error>'.$e->getMessage().'</error>');
            exit;
        }
    }

}