<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupCommand extends BaseCommand
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
        $config = $this->get('config');
        $eventDispatcher = $this->get('dispatcher');
        $docker = $this->get('docker');
        $logger = $this->get('logger');

        $logger->addInfo('Running cleanup for '.$config->getApplicationName());
        try {
            $eventDispatcher->dispatchPreCleanup();

            if ($config->get('[config][remove_exited_containers]')) {
                $this->runProcess($docker->getRemoveExitedContainersCommand());
            }

            if ($config->get('[config][remove_orphan_images]')) {
                $this->runProcess($docker->getRemoveOrphanImagesCommand());
            }

            $eventDispatcher->dispatchPostCleanup();
        } catch (\Exception $e) {
            $logger->addError($e->getMessage());
        }
    }
}
