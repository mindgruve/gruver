<?php

namespace Mindgruve\Gruver\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GruverConfig
{
    /**
     * @var array
     */
    protected $config;

    protected $envVars;

    public function __construct($gruverYaml = null)
    {
        if (!$gruverYaml) {
            $gruverYaml = $_SERVER['PWD'].'/gruver.yml';
        }

        if (!file_exists($gruverYaml)) {
            throw new \Exception('Gruver could not find a gruver.yml.');
        }

        $processor = new Processor();

        $current = Yaml::parse($gruverYaml);
        $default = Yaml::parse(__DIR__.'/../Resources/config/gruver.yml');

        $this->config = $processor->processConfiguration(new GruverConfigSchema(), array($default,$current));
        $this->envVars = new EnvironmentalVariables($this);
    }

    /**
     * @param $key
     * @return string|array
     * @throws \Exception
     */
    public function get($key)
    {

        $accessor = PropertyAccess::createPropertyAccessor();

        switch ($key) {
            case '[application][directory]':
                return $_SERVER['PWD'];
            case '[binaries][docker_compose]':
                return isset($this->config['binaries']['docker_compose']) ? $this->config['binaries']['docker_compose'] : 'docker-compose';
            case '[binaries][docker]':
                return isset($this->config['binaries']['docker']) ? $this->config['binaries']['docker'] : 'docker';
            default:
                return $accessor->getValue($this->config, $key);
        }
    }
}