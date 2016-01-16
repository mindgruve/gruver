<?php

namespace Mindgruve\Gruver\Config;

use Mockery\CountValidator\Exception;
use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{

    protected $config;

    public function __construct($gruverYaml)
    {
        if (!file_exists($gruverYaml)) {
            throw new Exception('Yaml File Does Not Exist - '.$gruverYaml);
        }

        $this->config = Yaml::parse($gruverYaml);
    }

}