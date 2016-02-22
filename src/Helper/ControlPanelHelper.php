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

        // grab control panel
        $gruverCP = $projectRepository->findOneBy(array('name' => 'control-panel'));

        $data = array();

        $projects = $projectRepository->findAll();
        foreach ($projects as $project) {
            if ($project == $gruverCP) {
                continue;
            }

            $services = $serviceRepository->findAll($project);
            foreach ($services as $service) {
                $releases = $releaseRepository->findAll($project, $service);
                foreach ($releases as $release) {
                    $data[$project->getName()][$service->getName()][] = array(
                        'tag' => $release->getTag(),
                        'uuid' => $release->getUuid(),
                    );
                }
            }
        }

        $output = $this->twig->render(
            'control-panel-index.php.twig',
            array('projects' => $data)
        );

        $fp = fopen('/vagrant/source-code/control-panel/application/web/index.php', 'w');
        fwrite($fp, $output);
    }
}