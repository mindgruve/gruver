<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Mindgruve\Gruver\Process\DockerProcess;
use Mindgruve\Gruver\Command;
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
        $config = $this->container['config'];
        $eventDispatcher = $this->container['dispatcher'];
        $docker = $this->container['docker'];
        $logger = $this->container['logger'];

        $logger->addInfo('Running cleanup for ' . $config->getApplicationName());
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
            $logger->addError($e->getMessage());
        }
    }
}