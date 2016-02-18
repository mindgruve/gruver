<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends BaseCommand
{
    const COMMAND = 'status';
    const DESCRIPTION = 'Deployment status.';

    public function configure()
    {
        $this->questionServiceName = 'What service are you interested in?  ';

        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addOption(
                'project_name',
                null,
                InputOption::VALUE_REQUIRED,
                'What do you want to name your project?'
            )
            ->addOption(
                'service_name',
                null,
                InputOption::VALUE_REQUIRED,
                'What service do you want to run?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getOption('service_name');

        $em = $this->get('entity_manager');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $service = $serviceRepository->findOneByName($serviceName);

        $output->writeln('');

        /*
         * Current Release
         */
        $currentRelease = $service->getCurrentRelease();
        $currentReleaseTag = 'n/a';
        if ($currentRelease) {
            $currentReleaseTag = $currentRelease->getTag();
        }
        $output->writeln('<info>Current Release:</info>  '.$currentReleaseTag);

        /*
         * Pending Release
         */
        $pendingRelease = $service->getPendingRelease();
        $pendingReleaseTag = 'n/a';
        if ($pendingRelease) {
            $pendingReleaseTag = $pendingRelease->getTag();
        }
        $output->writeln('<info>Pending Release:</info>  '.$pendingReleaseTag);

        /*
         * Rollback Release
         */
        $rollbackRelease = $service->getRollbackRelease();
        $rollbacktReleaseTag = 'n/a';
        if ($rollbackRelease) {
            $rollbacktReleaseTag = $rollbackRelease->getTag();
        }
        $output->writeln('<info>Rollback Release:</info>  '.$rollbacktReleaseTag);
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

            $date = 'n/a';
            if ($release->getCreatedAt()) {
                $date = $release->getCreatedAt()->format('n/j/y g:iA');
            }

            $rows[] = array($release->getTag(), $date, $status, $release->getContainerID());
        }

        $table = new Table($output);
        $table->setHeaders(array('Tag', 'Run Date', 'Status', 'Container', 'Heath Check'));
        $table->addRows($rows);
        $table->render();
    }
}
