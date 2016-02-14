<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{
    const COMMAND = 'status';
    const DESCRIPTION = 'Deployment status.';

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        $em = $this->get('entity_manager');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $service = $serviceRepository->findOneByName($serviceName);

        if (!$service) {
            $output->writeln('<error>Unknown service - ' . $serviceName . '</error>');
            exit;
        }

        $output->writeln('');
        $output->writeln('<info>Service</info> : ' . $serviceName);
        
        /**
         * Current Release
         */
        $currentRelease = $service->getCurrentRelease();
        $currentReleaseTag = 'n/a';
        if ($currentRelease) {
            $currentReleaseTag = $currentRelease->getTag();
        }
        $output->writeln('<info>Current Release:</info>  ' . $currentReleaseTag);

        /**
         * Pending Release
         */
        $pendingRelease = $service->getPendingRelease();
        $pendingReleaseTag = 'n/a';
        if ($pendingRelease) {
            $pendingReleaseTag = $pendingRelease->getTag();
        }
        $output->writeln('<info>Pending Release:</info>  ' . $pendingReleaseTag);

        /**
         * Rollback Release
         */
        $rollbackRelease = $service->getRollbackRelease();
        $rollbacktReleaseTag = 'n/a';
        if ($rollbackRelease) {
            $rollbacktReleaseTag = $rollbackRelease->getTag();
        }
        $output->writeln('<info>Rollback Release:</info>  ' . $rollbacktReleaseTag);
        $output->writeln('');

        $releases = $service->getReleases();
        $rows = array();
        foreach ($releases as $release) {

            $status = '';
            if ($pendingRelease && ($release->getId() == $pendingRelease->getId())) {
                $status = 'pending';
            }

            if ($currentRelease && ($release->getId() == $currentRelease->getId())) {
                $status = 'current';
            }

            if ($rollbackRelease && ($release->getId() == $rollbackRelease->getId())) {
                $status = 'rollback';
            }

            $rows[] = array($release->getTag(), $status);
        }

        $table = new Table($output);
        $table->setHeaders(array('Tag', 'Status', 'Container', 'Heath Check'));
        $table->addRows($rows);
        $table->render();
    }
}
