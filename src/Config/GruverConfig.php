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
        $defaults = Yaml::parse(__DIR__.'/../Resources/config/gruver.yml');

        $this->config = $processor->processConfiguration(new GruverConfigSchema(), array($current, $defaults));
        $this->envVars = new EnvironmentalVariables($this);
    }

    /**
     * @param $key
     * @return string|array
     * @throws \Exception
     */
    public function get($key)
    {
        switch ($key) {
            case 'application.directory':
                return $_SERVER['PWD'];
            case 'application.name':
                return $this->config['application']['name'];
            case 'binaries.docker_compose':
                return isset($this->config['binaries']['docker_compose']) ? $this->config['binaries']['docker_compose'] : 'docker-compose';
            case 'binaries.docker':
                return isset($this->config['binaries']['docker']) ? $this->config['binaries']['docker'] : 'docker';
            case 'events.pre_build':
                return $this->config['events']['pre_build'];
            case 'events.post_build':
                return $this->config['events']['post_build'];
            case 'events.pre_cleanup':
                return $this->config['events']['pre_cleanup'];
            case 'events.post_cleanup':
                return $this->config['events']['post_cleanup'];
            case 'cleanup.remove_exited_containers':
                return $this->config['cleanup']['remove_exited_containers'];
            case 'cleanup.remove_orphan_images':
                return $this->config['cleanup']['remove_orphan_images'];
            default:
                throw new \Exception('Configuration key not found - '.$key);
                break;
        }
    }
}