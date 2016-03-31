<?php

namespace Mindgruve\Gruver\Helper;

use Doctrine\ORM\EntityManager;
use Mindgruve\Gruver\Config\GruverConfig;
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

    /**
     * @var GruverConfig
     */
    protected $config;

    public function __construct(Twig_Environment $twig, EntityManager $em, GruverConfig $config)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->config = $config;
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
                    'service_id'   => $service->getId(),
                    'release_id'   => $release->getId(),
                    'release_uuid' => $release->getUuid(),
                    'hosts'        => $service->getPublicHosts(),
                    'ip'           => $release->getContainerIp(),
                    'port'         => $release->getContainerPort(),
                    'status'       => 'staging',
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
                'all_services'     => $allServices,
                'live_services'    => $liveServices,
                'staging_services' => $stagingServices
            )
        );

        $haproxyConfigFile = $this->config->get('[config][haproxy_cfg]');

        if (!$haproxyConfigFile || !file_exists($haproxyConfigFile)) {
            throw new \Exception('HAProxy Config File Missing');
        }

        $fp = fopen($haproxyConfigFile, 'w');
        fwrite($fp, $cfg);

        /**
         * Execute Update Config
         * @todo put into container
         * @todo test before switching config
         * @doto backup config before switching
         */
        $haproxyReloadCmd = $this->config->get('[config][haproxy_reload]');
        if (!$haproxyReloadCmd) {
            throw new \Exception('Reload Command Missing.');
        }

        exec($haproxyReloadCmd);
    }
}