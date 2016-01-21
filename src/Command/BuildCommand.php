<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{

    const COMMAND = 'build';
    const DESCRIPTION = 'Build a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new GruverConfig();
        $eventDispatcher = new EventDispatcher($config, $output);

        $output->writeln('<info>GRUVER: Building container for '.$config->get('[application][name]').'</info>');

        try {
            $eventDispatcher->dispatchPreBuild();
            $process = new Process($config->get('[binaries][docker_compose]').' build');
            $process->setTimeout(3600);
            $process->mustRun(
                function ($type, $buffer) use ($output) {
                    $output->write($buffer);
                }
            );
            $eventDispatcher->dispatchPostBuild();
        } catch (\Exception $e) {
            $output->write('<error>'.$e->getMessage().'</error>');
            exit;
        }
    }
}