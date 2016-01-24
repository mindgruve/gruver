<?php

namespace Mindgruve\Gruver\Config;

class EnvironmentalVariables
{

    protected $config;

    protected $GVR_APPLICATION;

    protected $GVR_RELEASE;

    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
        $this->GVR_APPLICATION = $this->config->getApplicationName();
        $this->GVR_RELEASE = '1.0.0';
    }

    public function buildExport()
    {
        $env = 'export GVR_APPLICATION=' . $this->GVR_APPLICATION . ';';
        $env .= ' export GVR_RELEASE=' . $this->GVR_RELEASE . ';';

        return $env;
    }
}