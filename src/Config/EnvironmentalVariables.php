<?php

namespace Mindgruve\Gruver\Config;

class EnvironmentalVariables
{

    protected $config;

    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
    }

    public function buildExport()
    {
        $applicationName = escapeshellarg($this->config->get('[application][name]'));
        $applicationDir = escapeshellarg($this->config->get('[application][directory]'));

        return 'export GRUVER_APPLICATION_NAME='.$applicationName;
    }

}