<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Mindgruve\Gruver\Helper\DateTimeHelper;
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
        $projectName = $input->getOption('project_name');
        $serviceName = $input->getOption('service_name');

        $em = $this->get('entity_manager');
        /*
         * Get Entities
         */
        $projectRepository = $em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');


        $project = $projectRepository->loadProjectByName($projectName);

        if (!$project) {
            $output->writeln('<error>Project ' . $projectName . ' does not exist </error>');

            return;
        }

        $service = $serviceRepository->loadServiceByName($project, $serviceName);


        /*
         * Check if Service Exists
         */
        if (!$service) {
            $output->writeln('<error>Service ' . $serviceName . ' does not exist </error>');

            return;
        }

        $output->writeln('');

        /*
         * Current Release
         */
        $currentRelease = $service->getCurrentRelease();
        $pendingRelease = $service->getPendingRelease();
        $rollbackRelease = $service->getRollbackRelease();

        $releases = $service->getReleases();
        $rows = array();
        foreach ($releases as $release) {
            $status = '';
            $tag = $release->getTag();

            if ($pendingRelease && ($release->getId() == $pendingRelease->getId())) {
                $status = '<comment>pending</comment>';
            }

            if ($currentRelease && ($release->getId() == $currentRelease->getId())) {
                $status = '<info>current</info>';
            }

            if ($rollbackRelease && ($release->getId() == $rollbackRelease->getId())) {
                $status = 'rollback';
            }

            $date = 'n/a';
            if ($release->getCreatedAt()) {
                $date = DateTimeHelper::humanTimeDiff($release->getCreatedAt()->getTimestamp());
            }

            $rows[] = array($tag . ' '. $status, $date, $release->getContainerID());
        }

        $table = new Table($output);
        $table->setHeaders(array('Tag', 'Run Date', 'Container', 'Heath Check'));
        $table->addRows($rows);
        $table->render();
    }
}
