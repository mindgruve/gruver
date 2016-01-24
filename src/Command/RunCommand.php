<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\DockerCompose;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    use GruverCommandTrait;

    const COMMAND = 'run';
    const DESCRIPTION = 'Run a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED,
                'What service do you want to run?'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        $config = new GruverConfig();
        $eventDispatcher = new EventDispatcher($config, $output);
        $dockerCompose = new DockerCompose($config);

        $output->writeln('<info>GRUVER: Running container for ' . $config->getApplicationName() . '</info>');

        try {
            $eventDispatcher->dispatchPreRun();

            /**
             * @todo Check that external links are running
             */
            $cmd = $dockerCompose->getRunCommand($serviceName);
            $this->mustRunProcess($cmd, $config, 3600, $output);
            $eventDispatcher->dispatchPostRun();

        } catch (\Exception $e) {
            $output->write('<error>' . $e->getMessage() . '</error>');
            exit;
        }
    }
}