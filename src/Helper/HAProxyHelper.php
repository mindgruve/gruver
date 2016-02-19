<?php

namespace Mindgruve\Gruver\Helper;

use Mindgruve\Gruver\Config\GruverConfig;

class HAProxyHelper
{

    protected $twig;

    protected $config;

    public function __construct(\Twig_Environment $twig, GruverConfig $config)
    {
        $this->twig = $twig;
        $this->config = $config;
    }

    public function render()
    {
        $config = $this->config->get('[haproxy]');

        $services = array();
        $services[] = array(
            'id' => 1,
            'hosts' => array(
                'mindgruve.com',
                'www.mindgruve.com',
            ),
            'ip' => '127.0.0.0',
            'port' => '1242'
        );

        return $this->twig->render('haproxy.cfg.twig', array('services' => $services, 'config' => $config));
    }

}