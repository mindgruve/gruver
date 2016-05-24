<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Entity\Service;
use Mindgruve\Gruver\Entity\StatusInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadConfigCommand extends BaseCommand
{
    const COMMAND = 'load-config';
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
        $services = $config->get('[project][public_services]');

        $em = $this->get('entity_manager');
        $projectRepository = $em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $em->getRepository('Mindgruve\Gruver\Entity\Service');

        $project = $projectRepository->loadProjectByName($projectName);
        if (!$project) {
            $output->writeln('<info>Adding PROJECT: '.$projectName);
            $project = new Project();
            $project->setName($projectName);
            $project->setStatus(StatusInterface::STATUS_ENABLED);
            $em->persist($project);
            $em->flush();
        }

        foreach ($services as $item) {
            $serviceName = $item['name'];
            $hosts = $item['hosts'];
            $ports = $item['ports'];

            /**
             * @todo open up so more flexible
             */

            if ($hosts == array()) {
                throw new \Exception('Each service must have a host right now');
            }

            if ($ports != array(80)) {
                throw new \Exception('Only port 80 is supported right now');
            }

            $service = $serviceRepository->findOneBy(array('name' => $serviceName, 'project' => $project));
            if (!$service) {

                $output->writeln('<info>Adding SERVICE: '.$serviceName);
                $service = new Service();
                $service->setStatus(StatusInterface::STATUS_ENABLED);

                if ($ports != array(80)) {
                    throw new \Exception('Only port 80 is supported at this time.');
                }
                $service->setName($serviceName);
                $service->setProject($project);
                $project->addService($service);
                $em->persist($service);
            }

            if ($service->getPublicHosts() != $hosts) {
                $service->setPublicHosts($hosts);
                $output->writeln('<info>Modifying hosts for SERVICE: '.$serviceName);
            }

            if ($service->getPublicPorts() != $ports) {
                $service->setPublicPorts($ports);
                $output->writeln('<info>Modifying ports for SERVICE: '.$serviceName);
            }

            $project->setConfigHash($config->getConfigHash());
            $em->flush();
        }
    }
}
