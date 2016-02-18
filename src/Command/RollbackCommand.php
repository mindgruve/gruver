<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends BaseCommand
{
    const COMMAND = 'rollback';
    const DESCRIPTION = 'Rollback a deployment and use previous container.';

    public function configure()
    {
        $this->questionServiceName = 'What service do you want to rollback?  ';

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

        /*
         * Container Service
         */
        $em = $this->get('entity_manager');

        /*
         * Get Entities
         */
        $projectRepository = $em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');

        $project = $projectRepository->loadProjectByName($projectName);

        /*
         * Check if Project Exists
         */
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

        $targetRelease = $service->getRollbackRelease();
        if ($targetRelease) {
            $service->setCurrentRelease($targetRelease);
            $em->flush();
        }
    }
}
