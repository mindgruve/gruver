<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{
    const COMMAND = 'status';
    const DESCRIPTION = 'Deployment status.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED,
                'What service do you want to run?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        $em = $this->get('entity_manager');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');
        $service = $serviceRepository->findOneByName($serviceName);

        if (!$service) {
            $output->writeln('<error>Unknown service - ' . $serviceName . '</error>');
            exit;
        }

        $output->writeln('');
        $output->writeln('<info>Service</info> : ' . $serviceName);

        $currentRelease = $service->getCurrentRelease();
        $currentReleaseTag = 'n/a';
        if ($currentRelease) {
            $currentReleaseTag = $currentRelease->getTag();
        }
        $output->writeln('<info>Current Release:</info>  ' . $currentReleaseTag);
        $output->writeln('');

        $releases = $service->getReleases();
        $rows = array();
        foreach ($releases as $release) {
            $rows[] = array($release->getTag(), $release->getStatus());
        }

        $table = new Table($output);
        $table->setHeaders(array('Tag', 'Status', 'Container'));
        $table->addRows($rows);
        $table->render();
    }
}
