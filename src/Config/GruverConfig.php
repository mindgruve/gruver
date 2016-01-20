<?php

namespace Mindgruve\Gruver\Config;

use Mockery\CountValidator\Exception;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class GruverConfig
{
    /**
     * @var array
     */
    protected $config;

    protected $envVars;

    public function __construct($gruverYaml)
    {
        if (!file_exists($gruverYaml)) {
            throw new Exception('Yaml File Does Not Exist - '.$gruverYaml);
        }

        $yaml = Yaml::parse($gruverYaml);
        $processor = new Processor();
        $this->config = $processor->processConfiguration(new GruverConfigSchema(), array($yaml));
        $this->envVars = new EnvironmentalVariables($this);
    }

    public function get($key)
    {
        switch ($key) {
            case 'application.name':
                return $this->config['application']['name'];
            case 'binaries.docker_compose':
                return isset($this->config['binaries']['docker_compose']) ? $this->config['binaries']['docker_compose'] : 'docker-compose';
        }
    }
}