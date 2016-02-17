<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Mindgruve\Gruver\Entity\Release;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends BaseCommand
{
    const COMMAND = 'up';
    const DESCRIPTION = 'Run a docker container.';

    public function configure()
    {
        $this->questionServiceName = 'What service do you want to bring up?  ';
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
        $serviceName = $input->getOption('service_name');
        $tag = $input->getOption('tag');

        /**
         * Container Service
         */
        $config = $this->get('config');
        $eventDispatcher = $this->get('dispatcher');
        $dockerCompose = $this->get('docker_compose');
        $logger = $this->get('logger');
        $em = $this->get('entity_manager');

        /**
         * Get Entities
         */
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $releaseRepository = $em->getRepository('Mindgruve\Gruver\Entity\Release');
        $service = $serviceRepository->findOneBy(array('name' => $serviceName));

        /**
         * Check if Service Exists
         */
        if (!$service) {
            $output->writeln('<error>Service ' . $serviceName . ' does not exist </error>');
            return;
        }

        /**
         * Check if Tag Exists
         */
        if ($releaseRepository->checkIfTagExistsForService($service, $tag)) {
            $output->writeln('<error>Service ' . $serviceName . ' already has tag ' . $tag . '</error>');
            return;
        }

        $oldRelease = $service->getCurrentRelease();
        $oldPendingRelease = $service->getPendingRelease();

        /**
         * Bring up service
         */
        try {
            $logger->addInfo('Running container for ' . $serviceName);
            $eventDispatcher->dispatchPreRun();
            $this->mustRunProcess($dockerCompose->getUpCommand($serviceName), $config, 3600, $output);

            $pendingRelease = new Release();
            $pendingRelease->setService($service);
            $pendingRelease->setTag($tag);

            if ($oldRelease) {
                $pendingRelease->setPreviousRelease($oldRelease);
                $oldRelease->setNextRelease($pendingRelease);
            }

            if ($oldPendingRelease) {
                $pendingRelease->setPreviousRelease($oldPendingRelease);
                $oldPendingRelease->setNextRelease($pendingRelease);
            }

            $service->setPendingRelease($pendingRelease);
            $service->addRelease($pendingRelease);
            $em->persist($pendingRelease);
            $em->flush();

            $eventDispatcher->dispatchPostRun();
        } catch (\Exception $e) {
            $logger->addError('Error encountered running docker-compose');
            $logger->addError($e->getMessage());
        }
    }
}
