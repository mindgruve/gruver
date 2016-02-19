<?php

namespace Mindgruve\Gruver\Helper;

class HAProxyHelper
{

    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render()
    {
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

        return $this->twig->render('haproxy.cfg.twig', array('services' => $services));
    }

}