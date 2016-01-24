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
    public function __construct($gruverYaml = null, $dockerComposeYaml = null)
    {
        $this->pwd = $_SERVER['PWD'];

        if (!$gruverYaml) {
            $gruverYaml = $this->pwd . '/gruver.yml';
        }

        if (!file_exists($gruverYaml)) {
            throw new \Exception('Gruver could not find a gruver.yml.');
        }

        $processor = new Processor();
        $this->gruverConfig = $processor->processConfiguration(
            new GruverConfigSchema(),
            array(
                Yaml::parse(__DIR__ . '/../Resources/config/gruver.yml'),
                Yaml::parse($gruverYaml)
            )
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