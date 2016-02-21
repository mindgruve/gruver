<?php

namespace Mindgruve\Gruver\Helper;

use Doctrine\ORM\EntityManager;
use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Entity\Project;
use Symfony\Component\Process\Process;

class HAProxyHelper
{

    protected $twig;

    protected $config;

    protected $em;

    public function __construct(\Twig_Environment $twig, GruverConfig $config, EntityManager $em)
    {
        $this->twig = $twig;
        $this->config = $config;
        $this->em = $em;
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
         */
        exec('/etc/init.d/haproxy restart');
    }
}