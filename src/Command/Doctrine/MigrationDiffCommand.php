<?php

namespace Mindgruve\Gruver\Command\Doctrine;

use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationDiffCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('doctrine:migrations:diff')
            ->setDescription('Generate a migration by comparing your current database to your mapping information.')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command generates a migration by comparing your current database to your mapping information:

    <info>%command.full_name%</info>

You can optionally specify a <comment>--editor-cmd</comment> option to open the generated file in your favorite editor:

    <info>%command.full_name% --editor-cmd=mate</info>
EOT
            )
            ->addOption(
                'filter-expression',
                null,
                InputOption::VALUE_OPTIONAL,
                'Tables which are filtered by Regular Expression.'
            )
            ->addOption('formatted', null, InputOption::VALUE_NONE, 'Format the generated SQL.')
            ->addOption('line-length', null, InputOption::VALUE_OPTIONAL, 'Max line length of unformatted lines.', 120);
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
        if ($input->getOption('filter-expression')) {
            $arguments['--filter-expression'] = $input->getOption('filter-expression');
        };
        if ($input->getOption('formatted')) {
            $arguments['--formatted'] = $input->getOption('formatted');
        };
        if ($input->getOption('line-length')) {
            $arguments['--line-length'] = $input->getOption('line-length');
        };
        $arguments['--configuration'] = __DIR__ . '/../../migrations.yml';

        $command = new DiffCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput($arguments), $output);

        return $returnCode;
    }
}