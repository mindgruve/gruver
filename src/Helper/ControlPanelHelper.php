<?php

namespace Mindgruve\Gruver\Helper;

use Doctrine\ORM\EntityManager;
use \Twig_Environment;
use Doctrine\Common\Persistence\ManagerRegistry;

class ControlPanelHelper
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @param Twig_Environment $twig
     * @param EntityManager $em
     */
    public function __construct(Twig_Environment $twig, EntityManager $em)
    {
        $this->twig = $twig;
        $this->em = $em;
    }

    public function update()
    {
        $projectRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Service');
        $releaseRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Release');

        $projects = $projectRepository->findAll();
        foreach ($projects as $project) {
            echo PHP_EOL . $project->getName() . PHP_EOL;
            $services = $serviceRepository->findAll($project);
            foreach ($services as $service) {
                echo $service->getName() . PHP_EOL;
                $releases = $releaseRepository->findAll($project, $service);
                foreach ($releases as $release) {
                    echo $release->getUuid() . PHP_EOL;
                }
            }
        }
    }
}