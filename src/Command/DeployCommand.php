<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends BaseCommand
{
    const COMMAND = 'deploy';
    const DESCRIPTION = 'Deploy an application.';


    public function configure()
    {
        $this->questionServiceName = 'What service do you want to deploy?  ';
        $this->questionTag = 'What tag do you want to deploy?  ';

        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addOption(
                'project_name',
                null,
                InputOption::VALUE_REQUIRED,
                'What project do you want to deploy?'
            )
            ->addOption(
                'service_name',
                null,
                InputOption::VALUE_REQUIRED,
                'What service do you want to deploy?'
            )
            ->addOption(
                'tag',
                null,
                InputOption::VALUE_REQUIRED,
                'What tag do you want to run?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getOption('project_name');
        $serviceName = $input->getOption('service_name');
        $tag = $input->getOption('tag');

        /*
         * Container Service
         */
        $em = $this->get('entity_manager');

        /*
         * Get Entities
         */
        $projectRepository = $em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $releaseRepository = $em->getRepository('Mindgruve\Gruver\Entity\Release');

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

        $targetRelease = $releaseRepository->findReleaseByTag($project, $service, $tag);
        if ($targetRelease) {
            $service->setCurrentRelease($targetRelease);
            $em->flush();
        }

        $haProxyHelper = $this->get('haproxy.helper');
        $haProxyHelper->updateConfig();
    }
}
