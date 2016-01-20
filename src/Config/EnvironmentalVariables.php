<?php

namespace Mindgruve\Gruver\Config;

class EnvironmentalVariables
{

    protected $config;

    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
    }

    public function export()
    {
        $name = escapeshellarg($this->config->get('application.name'));

        return 'export GRUVER_APPLICATION_NAME='.$name;
    }

}