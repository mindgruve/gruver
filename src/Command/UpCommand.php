<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Mindgruve\Gruver\Entity\Release;
use Mindgruve\Gruver\Entity\Service;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UpCommand extends Command
{
    const COMMAND = 'up';
    const DESCRIPTION = 'Run a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED,
                'What service do you want to run?'
            )
            ->addOption(
                'tag',
                null,
                InputOption::VALUE_REQUIRED,
                'What tag do you want to name this release?'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $service = $input->getArgument('service_name');
        $tag = $input->getOption('tag');

        $helper = $this->getHelper('question');

        // Double check service entered
        if (!$service) {
            $question = new Question('What service do you want bring up?  ');
            $service = $helper->ask($input, $output, $question);
        }

        // Double check tag entered
        if (!$tag) {
            $question = new Question('What do you want to tag this release?  ');
            $tag = $helper->ask($input, $output, $question);
        }

        $input->setOption('tag', $tag);
        $input->setArgument('service_name', $service);

        parent::initialize($input, $output);
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');
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
         * Get Service Entity
         */
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $service = $serviceRepository->getServiceOrCreate($serviceName);
        $oldRelease = $service->getCurrentRelease();

        /**
         * Bring up service
         */
        try {
            $logger->addInfo('Running container for ' . $config->getApplicationName());
            $eventDispatcher->dispatchPreRun();
            $this->mustRunProcess($dockerCompose->getUpCommand($serviceName), $config, 3600, $output);

            $currentRelease = new Release();
            $currentRelease->setService($service);
            $currentRelease->setTag($tag);
            $currentRelease->setStatus(Release::STATUS_PENDING);

            if ($oldRelease) {
                $currentRelease->setPreviousRelease($oldRelease);
                $oldRelease->setNextRelease($currentRelease);
                $oldRelease->setStatus(Release::STATUS_NULL);
            }

            $service->setCurrentRelease($currentRelease);
            $service->addRelease($currentRelease);
            $em->persist($currentRelease);
            $em->flush();

            $eventDispatcher->dispatchPostRun();
        } catch (\Exception $e) {
            $logger->addError('Error encountered running docker-compose');
            $logger->addError($e->getMessage());
        }
    }
}
