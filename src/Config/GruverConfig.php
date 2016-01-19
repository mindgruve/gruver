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
            case 'name':
                return $this->config['name'];
            case 'build.compose_binary':
                return $this->config['build']['compose_binary'];
        }
    }
}