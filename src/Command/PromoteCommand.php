<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Entity\Release;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PromoteCommand extends Command
{
    const COMMAND = 'promote';
    const DESCRIPTION = 'Promote a container to accept live traffic.';

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

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $serviceName = $input->getArgument('service_name');
        $tag = $input->getOption('tag');

        $this->container['env_vars'] = function ($c) use ($serviceName, $tag) {
            return new EnvironmentalVariables($c['config'], $serviceName, $tag);
        };
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

        $targetRelease = $service->getPendingRelease();

        if ($targetRelease) {

            $oldRelease = $targetRelease->getPreviousRelease();
            if ($oldRelease) {
                $targetRelease->setPreviousRelease($oldRelease);
                $oldRelease->setNextRelease($targetRelease);
            }

            $service->setCurrentRelease($targetRelease);
            $service->setPendingRelease($targetRelease->getNextRelease());
            $service->setRollbackRelease($oldRelease);
            $em->flush();
        }
    }
}
