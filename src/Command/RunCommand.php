<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $config = new GruverConfig();
        $eventDispatcher = new EventDispatcher($config, $output);
        $serviceName = $input->getArgument('service_name');

        $output->writeln('<info>GRUVER: Running container for ' . $config->getApplicationName() . '</info>');

        try {
            $eventDispatcher->dispatchPreRun();

            /**
             * @todo Check that external links are running
             */

            /**
             * @todo take the application name from the command argument instead
             */

            $cmd = $config->get('[config][docker_compose_binary]') . ' run -d ' . $serviceName;

            $this->mustRunProcess($cmd, $config, 3600, $output);
            $eventDispatcher->dispatchPostRun();

        } catch (\Exception $e) {
            $output->write('<error>' . $e->getMessage() . '</error>');
            exit;
        }
    }
}