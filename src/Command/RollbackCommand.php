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
        $serviceName = $input->getOption('service_name');

        /**
         * Container Service
         */
        $em = $this->get('entity_manager');

        /**
         * Get Entities
         */
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $service = $serviceRepository->findOneByName($serviceName);

        /**
         * Check if Service Exists
         */
        if (!$service) {
            $output->writeln('<error>Service ' . $serviceName . ' does not exist </error>');
            exit;
        }

        $pendingRelease = $service->getCurrentRelease();
        $targetRelease = $service->getRollbackRelease();

        if ($targetRelease) {

            $rollbackRelease = $targetRelease->getPreviousRelease();

            if ($rollbackRelease) {
                $targetRelease->setPreviousRelease($rollbackRelease);
                $rollbackRelease->setNextRelease($targetRelease);
            }


            $service->setCurrentRelease($targetRelease);
            $service->setPendingRelease($pendingRelease);
            $service->setRollbackRelease($rollbackRelease);
            $em->flush();
        }
    }
}
