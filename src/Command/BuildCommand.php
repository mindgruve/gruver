<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\DockerCompose;
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
        $dockerCompose = new DockerCompose($config);

        $output->writeln('<info>GRUVER: Building container for ' . $config->getApplicationName() . '</info>');

        try {
            $eventDispatcher->dispatchPreBuild();

            $cmd =$dockerCompose->getBuildCommand();
            $this->mustRunProcess($cmd, $config, 3600, $output);

            $eventDispatcher->dispatchPostBuild();
        } catch (\Exception $e) {
            $output->write('<error>' . $e->getMessage() . '</error>');
            exit;
        }
    }
}