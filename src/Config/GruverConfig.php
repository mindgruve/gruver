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
     * @var EnvironmentalVariables
     */
    protected $envVar;

    /**
     * @var string
     */
    protected $configHash = null;

    /**
     * @throws \Exception
     *
     * @internal param null|string $gruverYaml
     */
    public function __construct()
    {
        $this->pwd = $_SERVER['PWD'];
        $gruverYaml = $this->pwd.'/gruver.yml';

        /*
         * Load these configs, each one can potentially overwrite the configs before.
         *      (1) Config packaged with Gruver
         *      (2) The config loaded at /etc/gruver/gruver.yml
         *      (3) The local config ${PWD}/gruver.yml
         */
        $gruverConfigs = array();
        $gruverConfigs[] = Yaml::parse(__DIR__.'/../Resources/config/gruver.yml');
        if (file_exists('/etc/gruver/gruver.yml')) {
            $gruverConfigs[] = Yaml::parse('/etc/gruver/config.yml');
        }
        if (file_exists($gruverYaml)) {
            $this->configHash = sha1_file($gruverYaml);
            $gruverConfigs[] = Yaml::parse($gruverYaml);
        }

        /*
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
    public function getConfigHash()
    {
        return $this->configHash;
    }


    /**
     * @param $key
     *
     * @return string|array
     *
     * @throws \Exception
     */
    public function get($key)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        switch ($key) {
            case '[application][directory]':
                return $this->pwd;

            default:
                return $accessor->getValue($this->gruverConfig, $key);
        }
    }
}
