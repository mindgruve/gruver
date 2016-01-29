<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Process\DockerComposeProcess;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
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
        $config = new GruverConfig();
        $eventDispatcher = new EventDispatcher($config, $output);
        $dockerCompose = new DockerComposeProcess($config);

        try {
            $output->writeln('<info>GRUVER: Running container for ' . $config->getApplicationName() . '</info>');
            $eventDispatcher->dispatchPreRun();
            $this->mustRunProcess($dockerCompose->getUpCommand($serviceName), $config, 3600, $output);
            $eventDispatcher->dispatchPostRun();

        } catch (\Exception $e) {
            $output->write('<error>Error encountered running docker-compose</error>');
            $output->write($e->getMessage());
            exit;
        }
    }
}