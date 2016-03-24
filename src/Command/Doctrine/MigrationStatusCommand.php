<?php

namespace Mindgruve\Gruver\Command\Doctrine;

use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationStatusCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('doctrine:migrations:status')
            ->setDescription('View the status of a set of migrations.')
            ->addOption(
                'show-versions',
                null,
                InputOption::VALUE_NONE,
                'This will display a list of all available migrations and their status'
            )
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command outputs the status of a set of migrations:

    <info>%command.full_name%</info>

You can output a list of all available migrations and their status with <comment>--show-versions</comment>:

    <info>%command.full_name% --show-versions</info>
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
        if ($input->getOption('show-versions')) {
            $arguments['--show-versions'] = $input->getArgument('show-versions');
        };
        $arguments['--configuration'] = __DIR__ . '/../../../migrations.yml';

        $command = new StatusCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput($arguments), $output);

        return $returnCode;
    }
}