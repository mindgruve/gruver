<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends Command
{
    const COMMAND = 'up';
    const DESCRIPTION = 'Run a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'service_name',
                InputArgument::OPTIONAL,
                'What service do you want to run?'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');
        $config = $this->container['config'];
        $eventDispatcher = $this->container['dispatcher'];
        $dockerCompose = $this->container['docker_compose'];
        $logger = $this->container['logger'];

        try {
            $logger->addInfo('Running container for '.$config->getApplicationName());
            $eventDispatcher->dispatchPreRun();
            $this->mustRunProcess($dockerCompose->getUpCommand($serviceName), $config, 3600, $output);
            $eventDispatcher->dispatchPostRun();
        } catch (\Exception $e) {
            $logger->addError('Error encountered running docker-compose');
            $logger->addError($e->getMessage());
        }
    }
}
