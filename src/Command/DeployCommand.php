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
        $serviceName = $input->getOption('service_name');
        $tag = $input->getOption('service_name');

        /*
         * Container Service
         */
        $em = $this->get('entity_manager');

        /*
         * Get Entities
         */
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $service = $serviceRepository->findOneBy(array('name' => $serviceName));

        /*
         * Check if Service Exists
         */
        if (!$service) {
            $output->writeln('<error>Service '.$serviceName.' does not exist </error>');

            return;
        }

        $targetRelease = $service->getPendingRelease();
        if ($targetRelease) {
            $service->setCurrentRelease($targetRelease);
            $em->flush();
        }
    }


}
