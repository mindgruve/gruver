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
    protected $GVR_PROJECT;

    /**
     * @var string
     */
    protected $GVR_SERVICE;

    /**
     * @var string
     */
    protected $GVR_RELEASE;

    /**
     * @param GruverConfig $config
     * @param string       $serviceName
     * @param string       $tag
     */
    public function __construct(GruverConfig $config, $projectName = null, $serviceName = null, $tag = null)
    {
        $this->config = $config;
        $this->GVR_PROJECT = $projectName;
        $this->GVR_SERVICE = $serviceName;
        $this->GVR_RELEASE = $tag;
    }

    /**
     * @return string
     */
    public function buildExport()
    {
        $env = '';

        if ($this->GVR_PROJECT) {
            $env .= ' export GVR_PROJECT='.$this->GVR_PROJECT.';';
        }
        if ($this->GVR_SERVICE) {
            $env .= ' export GVR_SERVICE='.$this->GVR_SERVICE.';';
        }
        if ($this->GVR_RELEASE) {
            $env .= ' export GVR_RELEASE='.$this->GVR_RELEASE.';';
        }

        return $env;
    }

    public function getProjectName()
    {
        return $this->GVR_PROJECT;
    }

    public function getServiceName()
    {
        return $this->GVR_SERVICE;
    }

    public function getRelease()
    {
        return $this->GVR_RELEASE;
    }
}
