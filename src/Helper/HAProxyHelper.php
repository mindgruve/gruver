<?php

namespace Mindgruve\Gruver\Helper;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Entity\Project;
use Symfony\Component\Process\Process;

class HAProxyHelper
{

    protected $twig;

    protected $config;

    public function __construct(\Twig_Environment $twig, GruverConfig $config)
    {
        $this->twig = $twig;
        $this->config = $config;
    }

    public function updateConfig(Project $project)
    {
        $services = $project->getServices();

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