<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Process\DockerComposeProcess;
use Mindgruve\Gruver\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{

    use GruverCommandTrait;

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
        $config = new GruverConfig();
        $eventDispatcher = new EventDispatcher($config, $output);
        $dockerCompose = new DockerComposeProcess($config);

        try {
            $output->writeln('<info>GRUVER: Building container for ' . $config->getApplicationName() . '</info>');
            $eventDispatcher->dispatchPreBuild();
            $this->mustRunProcess($dockerCompose->getBuildCommand(), $config, 3600, $output);
            $eventDispatcher->dispatchPostBuild();
        } catch (\Exception $e) {
            $output->write('<error>Error encountered running docker-compose</error>');
            $output->write($e->getMessage());
        }
    }
}