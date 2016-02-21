<?php

namespace Mindgruve\Gruver\Helper;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Twig_Environment;

class HAProxyHelper
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(Twig_Environment $twig, ManagerRegistry $registry)
    {
        $this->twig = $twig;
        $this->em = $registry->getManager();
    }

    public function updateConfig()
    {

        $projectRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Project');
        $serviceRepository = $this->em->getRepository('Mindgruve\Gruver\Entity\Service');

        $services = array();

        $projects = $projectRepository->findAll();
        foreach ($projects as $project) {
            $projectServices = $serviceRepository->findAll($project);
            foreach ($projectServices as $projectService) {
                $services[] = $projectService;
            }
        }

        $liveServices = array();
        $stagingServices = array();
        $allServices = array();
        foreach ($services as $service) {

            $currentRelease = $service->getCurrentRelease();
            $releases = $service->getReleases();
            foreach ($releases as $release) {

                /**
                 * @var \Mindgruve\Gruver\Entity\Release $release
                 */
                $item = array(
                    'service_id' => $service->getId(),
                    'release_id' => $release->getId(),
                    'hosts' => $service->getPublicHosts(),
                    'ip' => $release->getContainerIp(),
                    'port' => $release->getContainerPort(),
                    'status' => 'staging',
                );

                if ($release == $currentRelease) {
                    $liveServices[] = $item;
                } else {
                    $stagingServices[] = $item;
                }
                $allServices[] = $item;
            }
        }

        $cfg = $this->twig->render(
            'haproxy.cfg.twig',
            array(
                'all_services' => $allServices,
                'live_services' => $liveServices,
                'staging_services' => $stagingServices
            )
        );

        $fp = fopen('/etc/haproxy/haproxy.cfg', 'w');
        fwrite($fp, $cfg);

        /**
         * Execute Update Config
         * @todo put into container
         * @todo test before switching config
         * @doto backup config before switching
         */
        exec('/etc/init.d/haproxy restart');
    }
}