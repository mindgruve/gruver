<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    const COMMAND = 'build';
    const DESCRIPTION = 'Build a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'service_name',
                InputArgument::OPTIONAL,
                'What service do you want to build?'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->container['config'];
        $eventDispatcher = $this->container['dispatcher'];
        $dockerCompose = $this->container['docker_compose'];
        $logger = $this->container['logger'];

        try {
            $logger->addInfo('Building container for '.$config->getApplicationName());
            $eventDispatcher->dispatchPreBuild();
            $this->mustRunProcess($dockerCompose->getBuildCommand(), $config, 3600, $output);
            $eventDispatcher->dispatchPostBuild();
        } catch (\Exception $e) {
            $logger->addError('Error encountered running docker-compose');
            $logger->addError($e->getMessage());
        }
    }
}
