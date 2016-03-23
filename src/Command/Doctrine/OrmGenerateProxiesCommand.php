<?php

namespace Mindgruve\Gruver\Command\Doctrine;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;

class OrmGenerateProxiesCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:orm:generate-proxies')
            ->setDescription('Generates proxy classes for entity classes.')
            ->addOption(
                'filter',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'A string pattern used to match entities that should be processed.'
            )
            ->addArgument(
                'dest-path',
                InputArgument::OPTIONAL,
                'The path to generate your proxy classes. If none is provided, it will attempt to grab from configuration.'
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

        $command = new GenerateProxiesCommand();
        $command->setHelperSet($helperSet);

        $arguments = array();
        if ($input->getOption('filter')) {
            $arguments['--filter'] = $input->getOption('filter');
        };
        if ($input->getArgument('dest-path')) {
            $arguments['dest-path'] = $input->getArgument('dest-path');
        };
        $returnCode = $command->run(new ArrayInput($arguments), $output);

        return $returnCode;
    }
}
