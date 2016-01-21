<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CleanupCommand extends Command
{
    const COMMAND = 'cleanup';
    const DESCRIPTION = 'Remove unused containers.';

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

        $output->writeln('<info>GRUVER: Running cleanup for '.$config->get('[application][name]').'</info>');

        try {
            $eventDispatcher->dispatchPreCleanup();

            if($config->get('[cleanup][remove_exited_containers]')){
                $process = new Process($config->get('[binaries][docker]').' rm -v $(docker ps -a -q -f status=exited)');
                $process->setTimeout(600);
                $process->run();
            }

            if($config->get('[cleanup][remove_orphan_images]')){
                $process = new Process($config->get('[binaries][docker]').' rmi $(docker images -f "dangling=true" -q)');
                $process->setTimeout(3600);
                $process->run();
            }

            $eventDispatcher->dispatchPostCleanup();
        } catch (\Exception $e) {
            $output->write('<error>'.$e->getMessage().'</error>');
            exit;
        }


    }
}