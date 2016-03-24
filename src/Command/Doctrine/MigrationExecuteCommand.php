<?php

namespace Mindgruve\Gruver\Command\Doctrine;

use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationExecuteCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('doctrine:migrations:execute')
            ->setDescription('Execute a single migration version up or down manually.')
            ->addArgument('version', InputArgument::REQUIRED, 'The version to execute.', null)
            ->addOption(
                'write-sql',
                null,
                InputOption::VALUE_NONE,
                'The path to output the migration SQL file instead of executing it.'
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('up', null, InputOption::VALUE_NONE, 'Execute the migration up.')
            ->addOption('down', null, InputOption::VALUE_NONE, 'Execute the migration down.')
            ->addOption('query-time', null, InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command executes a single migration version up or down manually:

    <info>%command.full_name% YYYYMMDDHHMMSS</info>

If no <comment>--up</comment> or <comment>--down</comment> option is specified it defaults to up:

    <info>%command.full_name% YYYYMMDDHHMMSS --down</info>

You can also execute the migration as a <comment>--dry-run</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --dry-run</info>

You can output the would be executed SQL statements to a file with <comment>--write-sql</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --write-sql</info>

Or you can also execute the migration without a warning message which you need to interact with:

    <info>%command.full_name% --no-interaction</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->get('entity_manager');

        $helperSet = new HelperSet();
        $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($em), 'em');

        $arguments = array();
        if ($input->getOption('write-sql')) {
            $arguments['--write-sql'] = $input->getOption('write-sql');
        };
        if ($input->getOption('dry-run')) {
            $arguments['--dry-run'] = $input->getOption('dry-run');
        };
        if ($input->getOption('up')) {
            $arguments['--up'] = $input->getOption('up');
        };
        if ($input->getOption('down')) {
            $arguments['--down'] = $input->getOption('down');
        };
        if ($input->getOption('query-time')) {
            $arguments['--query-time'] = $input->getOption('query-time');
        };
        $arguments['--configuration'] = __DIR__ . '/../../../migrations.yml';

        $command = new ExecuteCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput($arguments), $output);

        return $returnCode;
    }
}