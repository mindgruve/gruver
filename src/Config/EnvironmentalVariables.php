<?php

namespace Mindgruve\Gruver\Config;

class EnvironmentalVariables
{
    /**
     * @var GruverConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $GVR_APPLICATION;

    /**
     * @var string
     */
    protected $GVR_RELEASE;

    /**
     * @param GruverConfig $config
     */
    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
        $this->GVR_APPLICATION = $this->config->getApplicationName();
        $this->GVR_RELEASE = '1.0.0';
    }

    /**
     * @return string
     */
    public function buildExport()
    {
        $env = 'export GVR_APPLICATION='.$this->GVR_APPLICATION.';';
        $env .= ' export GVR_RELEASE='.$this->GVR_RELEASE.';';

        return $env;
    }
}
