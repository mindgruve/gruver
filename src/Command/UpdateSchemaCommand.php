<?php

namespace Mindgruve\Gruver\Command;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSchemaCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:schema:update')
            ->setDescription('Executes (or dumps) the SQL needed to generate the database schema')
            ->addOption(
                'complete',
                null,
                InputOption::VALUE_NONE,
                'If defined, all assets of the database which are not relevant to the current metadata will be dropped.'
            )
            ->addOption(
                'dump-sql',
                null,
                InputOption::VALUE_NONE,
                'Instead of trying to apply generated SQLs into EntityManager Storage Connection, output them.'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Causes the generated SQL statements to be physically executed against your database.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->container['entity.manager'];

        $helperSet = new HelperSet();
        $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($em), 'em');

        $arguments = array();
        if ($input->getOption('dump-sql')) {
            $arguments['--dump-sql'] = $input->getOption('dump-sql');
        };
        if ($input->getOption('complete')) {
            $arguments['--complete'] = $input->getOption('complete');
        };
        if ($input->getOption('force')) {
            $arguments['--force'] = $input->getOption('force');
        };

        $command = new UpdateCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput($arguments), $output);
    }
}
