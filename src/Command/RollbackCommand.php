<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends Command
{
    const COMMAND = 'rollback';
    const DESCRIPTION = 'Rollback a deployment and use previous container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED,
                'What service do you want to promote?'
            )
            ->addOption(
                'tag',
                null,
                InputOption::VALUE_REQUIRED,
                'What tag do you want to promote?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');
        $tag = $input->getOption('tag');

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

            if($rollbackRelease){
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
