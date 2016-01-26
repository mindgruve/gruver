<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Mindgruve\Gruver\Process\DockerProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CleanupCommand extends Command
{
    use GruverCommandTrait;

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
        $docker = new DockerProcess($config);

        $output->writeln('<info>GRUVER: Running cleanup for ' . $config->getApplicationName() . '</info>');

        try {
            $eventDispatcher->dispatchPreCleanup();

            if ($config->get('[config][remove_exited_containers]')) {
                $this->runProcess($docker->getRemoveExitedContainersCommand(), $config);
            }

            if ($config->get('[config][remove_orphan_images]')) {
                $this->runProcess($docker->getRemoveOrphanImagesCommand(), $config);
            }

            $eventDispatcher->dispatchPostCleanup();
        } catch (\Exception $e) {
            $output->write('<error>' . $e->getMessage() . '</error>');
            exit;
        }
    }
}