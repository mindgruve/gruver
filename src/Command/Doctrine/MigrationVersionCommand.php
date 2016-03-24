<?php

namespace Mindgruve\Gruver\Command\Doctrine;

use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationVersionCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('doctrine:migrations:version')
            ->setDescription('Manually add and delete migration versions from the version table.')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version to add or delete.', null)
            ->addOption('add', null, InputOption::VALUE_NONE, 'Add the specified version.')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete the specified version.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Apply to all the versions.')
            ->addOption('range-from', null, InputOption::VALUE_OPTIONAL, 'Apply from specified version.')
            ->addOption('range-to', null, InputOption::VALUE_OPTIONAL, 'Apply to specified version.')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command allows you to manually add, delete or synchronize migration versions from the version table:

    <info>%command.full_name% YYYYMMDDHHMMSS --add</info>

If you want to delete a version you can use the <comment>--delete</comment> option:

    <info>%command.full_name% YYYYMMDDHHMMSS --delete</info>

If you want to synchronize by adding or deleting all migration versions available in the version table you can use the <comment>--all</comment> option:

    <info>%command.full_name% --add --all</info>
    <info>%command.full_name% --delete --all</info>

If you want to synchronize by adding or deleting some range of migration versions available in the version table you can use the <comment>--range-from/--range-to</comment> option:

    <info>%command.full_name% --add --range-from=YYYYMMDDHHMMSS --range-to=YYYYMMDDHHMMSS</info>
    <info>%command.full_name% --delete --range-from=YYYYMMDDHHMMSS --range-to=YYYYMMDDHHMMSS</info>

You can also execute this command without a warning message which you need to interact with:

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
        if ($input->getArgument('version')) {
            $arguments['version'] = $input->getArgument('version');
        };
        if ($input->getOption('add')) {
            $arguments['--add'] = $input->getOption('add');
        };
        if ($input->getOption('delete')) {
            $arguments['--delete'] = $input->getOption('delete');
        };
        if ($input->getOption('all')) {
            $arguments['--all'] = $input->getOption('all');
        };
        if ($input->getOption('range-from')) {
            $arguments['--range-from'] = $input->getOption('range-from');
        };
        if ($input->getOption('range-to')) {
            $arguments['--range-to'] = $input->getOption('range-to');
        };
        $arguments['--configuration'] = __DIR__ . '/../../migrations.yml';

        $command = new VersionCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput($arguments), $output);

        return $returnCode;
    }
}