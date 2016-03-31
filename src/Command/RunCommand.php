<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Mindgruve\Gruver\Entity\Release;
use Mindgruve\Gruver\Entity\StatusInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webpatser\Uuid\Uuid;

class RunCommand extends BaseCommand
{
    const COMMAND = 'run';
    const DESCRIPTION = 'Run a docker container.';

    public function configure()
    {
        $this->questionServiceName = 'What service do you want to run?  ';
        $this->questionTag = 'What do you want to tag this release?  ';

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
            )
            ->addOption(
                'tag',
                null,
                InputOption::VALUE_REQUIRED,
                'What tag do you want to name this release?'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getOption('project_name');
        $serviceName = $input->getOption('service_name');
        $tag = $input->getOption('tag');

        /*
         * Container Service
         */
        $config = $this->get('config');
        $eventDispatcher = $this->get('dispatcher');
        $dockerCompose = $this->get('docker_compose');
        $docker = $this->get('docker');
        $logger = $this->get('logger');
        $em = $this->get('entity_manager');

        /*
         * Get Entities
         */
        $projectRepository = $em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $releaseRepository = $em->getRepository('Mindgruve\Gruver\Entity\Release');


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

        /*
         * Check if Tag Exists
         */
        if ($releaseRepository->checkIfTagExists($project, $service, $tag)) {
            $output->writeln('<error>Service ' . $serviceName . ' already has tag ' . $tag . '</error>');

            return;
        }

        /*
         * Bring up service
         */
        try {
            $logger->addInfo('Running container for ' . $serviceName);
            $eventDispatcher->dispatchPreRun();

            $uuid = Uuid::generate();
            $this->mustRunProcess($dockerCompose->getRunCommand($serviceName, $uuid), 3600, $output);

            $release = new Release();
            $release->setProject($project);
            $release->setService($service);
            $release->setStatus(StatusInterface::STATUS_ENABLED);
            $release->setTag($tag);
            $release->setUuid($uuid);

            $process = $this->mustRunProcess($docker->getContainerIdByGruverUUIDCommand($uuid));
            $containerId = trim($process->getOutput());

            $release->setContainerId($containerId);

            $process = $this->mustRunProcess($docker->getContainerPortsByGruverUUIDCommand($uuid));
            $output = trim($process->getOutput());
            if (preg_match('/(.*):(.*)->(.*)/', $output, $matches)) {
                $ip = $matches[1];
                $port = $matches[2];

                $release->setContainerIp($ip);
                $release->setContainerPort($port);
            } else {
                throw new \Exception('IP:Port needed for each container');
            }

            $mostRecentRelease = $service->getMostRecentRelease();
            if ($mostRecentRelease) {
                $release->setPreviousRelease($mostRecentRelease);
                $mostRecentRelease->setNextRelease($release);
            }

            $service->setMostRecentRelease($release);
            $service->addRelease($release);
            $em->persist($release);
            $em->flush();

            /**
             * Update HAProxy Config
             */
            $haProxyHelper = $this->get('haproxy.helper');
            $haProxyHelper->updateConfig();

            $eventDispatcher->dispatchPostRun();
        } catch (\Exception $e) {
            $logger->addError('Error encountered running docker-compose');
            $logger->addError($e->getMessage());
        }
    }
}
