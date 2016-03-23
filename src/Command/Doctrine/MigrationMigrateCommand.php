<?php

namespace Mindgruve\Gruver\Command\Doctrine;

use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationMigrateCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('doctrine:migrations:migrate')
            ->setDescription('Execute a migration to a specified version or the latest available version.')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.', 'latest')
            ->addOption('write-sql', null, InputOption::VALUE_NONE, 'The path to output the migration SQL file instead of executing it.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('query-time', null, InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->addOption('allow-no-migration', null, InputOption::VALUE_NONE, 'Don\'t throw an exception if no migration is available (CI).')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command executes a migration to a specified version or the latest available version:

    <info>%command.full_name%</info>

You can optionally manually specify the version you wish to migrate to:

    <info>%command.full_name% YYYYMMDDHHMMSS</info>

You can specify the version you wish to migrate to using an alias:

    <info>%command.full_name% prev</info>

You can also execute the migration as a <comment>--dry-run</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --dry-run</info>

You can output the would be executed SQL statements to a file with <comment>--write-sql</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --write-sql</info>

Or you can also execute the migration without a warning message which you need to interact with:

    <info>%command.full_name% --no-interaction</info>

You can also time all the different queries if you wanna know which one is taking so long:

    <info>%command.full_name% --query-time</info>
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
        $helperSet->set($this->getHelper('dialog'), 'dialog');

        $arguments = array();
        if ($input->getArgument('version')) {
            $arguments['version'] = $input->getArgument('version');
        };
        if ($input->getOption('write-sql')) {
            $arguments['--write-sql'] = $input->getOption('write-sql');
        };
        if ($input->getOption('dry-run')) {
            $arguments['--dry-run'] = $input->getOption('dry-run');
        };
        if ($input->getOption('query-time')) {
            $arguments['--query-time'] = $input->getOption('query-time');
        };
        if ($input->getOption('allow-no-migration')) {
            $arguments['--allow-no-migration'] = $input->getOption('allow-no-migration');
        };
        $arguments['--configuration'] = __DIR__ . '/../../../migrations.yml';

        $command = new MigrateCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput($arguments), $output);

        return $returnCode;
    }


}