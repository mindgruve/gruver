<?php

namespace Mindgruve\Gruver\Command;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DoctrineGenerateProxiesCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:generate-proxies')
            ->setDescription('Generates proxy classes for entity classes.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->get('entity.manager');

        $helperSet = new HelperSet();
        $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($em), 'em');

        $command = new GenerateProxiesCommand();
        $command->setHelperSet($helperSet);
        $returnCode = $command->run(new ArrayInput(array()), $output);
    }
}
