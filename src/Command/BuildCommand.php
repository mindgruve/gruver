<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends BaseCommand
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
        $config = $this->get('config');
        $eventDispatcher = $this->get('dispatcher');
        $dockerCompose = $this->get('docker_compose');
        $logger = $this->get('logger');

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
