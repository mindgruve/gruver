<?php

namespace Mindgruve\Gruver;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        $definition = $command->getDefinition();
        $definition->addOption(
            new InputOption(
                'gruver_file',
                'g',
                InputOption::VALUE_OPTIONAL,
                'Location of gruver.yml file'
            )
        );

        return parent::doRunCommand($command, $input, $output);
    }
}
