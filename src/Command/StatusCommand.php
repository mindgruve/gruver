<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Mindgruve\Gruver\Helper\DateTimeHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
        $releaseRepository = $em->getRepository('Mindgruve\Gruver\Entity\Release');

        $project = $projectRepository->loadProjectByName($projectName);

        if (!$project) {
            $output->writeln('<error>Project '.$projectName.' does not exist </error>');

            return;
        }

        /**
         * Check if Configuration needs to be reloaded
         */
        $this->checkConfigHash($project, $input, $output);

        $service = $serviceRepository->loadServiceByName($project, $serviceName);


        /*
         * Check if Service Exists
         */
        if (!$service) {
            $output->writeln('<error>Service '.$serviceName.' does not exist </error>');

            return;
        }

        $output->writeln('');

        /*
         * Current Release
         */
        $currentRelease = $service->getCurrentRelease();
        $pendingRelease = $service->getPendingRelease();
        $rollbackRelease = $service->getRollbackRelease();

        $releases = $releaseRepository->findAll($project, $service);
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

            $rows[] = array(
                $tag.' '.$status,
                $date,
                $release->getContainerID(),
                $release->getContainerIp(),
                $release->getContainerPort(),
                $release->getId(),
                $release->getUuid(),
            );
        }

        $table = new Table($output);
        $table->setHeaders(array('Tag', 'Run Date', 'Container', 'IP', 'Port', 'ID', 'UUID'));
        $table->addRows($rows);
        $table->render();
    }
}
