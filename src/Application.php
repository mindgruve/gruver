<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\GruverConfig;
use Pimple\Container;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    protected $eventDispatcher;

    protected $gruverConfig;

    protected $logHandler;

    protected $container;

    protected function init($output, $gruverYaml = null, $dockerComposeYaml = null)
    {
        $this->container = new Container();
        $this->gruverConfig = new GruverConfig($gruverYaml, $dockerComposeYaml);
        $this->eventDispatcher = new EventDispatcher($this->gruverConfig, $output);
    }
}