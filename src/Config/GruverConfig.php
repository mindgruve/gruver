<?php

namespace Mindgruve\Gruver\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GruverConfig
{
    /**
     * @var string
     */
    protected $pwd;

    /**
     * @var array
     */
    protected $gruverConfig;

    /**
     * @var array
     */
    protected $dockerCompose;

    /**
     * @var array
     */
    protected $envVar;

    /**
     * @param null|string $gruverYaml
     * @throws \Exception
     */
    public function __construct($gruverYaml = null)
    {
        $this->pwd = $_SERVER['PWD'];
        $gruverYaml = $gruverYaml ? $gruverYaml : $this->pwd . '/gruver.yml';

        /**
         * Load these configs, each one can potentially overwrite the configs before.
         *      (1) Config packaged with Gruver
         *      (2) The config loaded at /etc/gruver/gruver.yml
         *      (3) The local config ${PWD}/gruver.yml
         */
        $gruverConfigs[] = Yaml::parse(__DIR__ . '/../Resources/config/gruver.yml');
        if (file_exists('/etc/gruver/gruver.yml')) {
            $gruverConfigs[] = Yaml::parse('/etc/gruver/gruver.yml');
        }
        if (file_exists($gruverYaml)) {
            $gruverConfigs[] = Yaml::parse($gruverYaml);
        }

        /**
         * Process to validate schema
         */
        $processor = new Processor();
        $this->gruverConfig = $processor->processConfiguration(
            new GruverConfigSchema(),
            $gruverConfigs
        );
    }

    /**
     * @return string
     */
    public function getApplicationName()
    {
        return $this->get('[application][name]');
    }

    /**
     * @return string
     */
    public function getEnvironmentalVariableExport()
    {
        if (!$this->envVar) {
            $this->envVar = new EnvironmentalVariables($this);
        }

        return $this->envVar->buildExport();
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
                return $this->pwd;
            case '[binaries][docker_compose]':
                return isset($this->gruverConfig['binaries']['docker_compose']) ? $this->gruverConfig['binaries']['docker_compose'] : 'docker-compose';
            case '[binaries][docker]':
                return isset($this->gruverConfig['binaries']['docker']) ? $this->gruverConfig['binaries']['docker'] : 'docker';
            default:
                return $accessor->getValue($this->gruverConfig, $key);
        }
    }
}