<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Entity\Service;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseCommand
{
    const COMMAND = 'init';
    const DESCRIPTION = 'Initialize a gruver project.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->get('config');

        $projectName = $config->get('[project][name]');
        $services = $config->get('[project][services]');

        $em = $this->get('entity_manager');
        $projectRepository = $em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');

        $project = $projectRepository->findByName($projectName);
        if (!$project) {
            $output->writeln('<info>Adding PROJECT: '.$projectName);
            $project = new Project();
            $project->setName($projectName);
            $em->persist($project);
            $em->flush();
        }

        foreach ($services as $serviceName) {
            $service = $serviceRepository->findOneBy(array('name' => $serviceName, 'project' => $project));
            if (!$service) {
                $output->writeln('<info>Adding SERVICE: '.$serviceName);
                $service = new Service();
                $service->setName($serviceName);
                $service->setProject($project);
                $project->addService($service);
                $em->persist($service);
                $em->flush();
            }
        }
    }
}
