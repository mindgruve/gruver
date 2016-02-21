<?php

namespace Mindgruve\Gruver\Helper;

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
     * @param ManagerRegistry $registry
     */
    public function __construct(Twig_Environment $twig, ManagerRegistry $registry)
    {
        $this->twig = $twig;
        $this->em = $registry->getManager();
    }

    public function update()
    {
        $projectRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Service');

        $projects = $projectRepository->findAll();

        foreach($projects as $project){
            echo $project->getName();
        }
    }
}