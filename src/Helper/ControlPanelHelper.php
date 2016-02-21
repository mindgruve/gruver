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

        $projects = $projectRepository->findAll();

        foreach($projects as $project){
            echo $project->getName();
        }
    }
}