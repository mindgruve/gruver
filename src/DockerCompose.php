<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\GruverConfig;

class DockerCompose
{

    /**
     * @var string
     */
    protected $pwd;

    /**
     * @var array
     */
    protected $files;

    /**
     * @var GruverConfig
     */
    protected $config;

    /**
     * @param GruverConfig $config
     * @throws \Exception
     */
    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
        $files = $config->get('[config][docker_compose_files]');
        $this->pwd = $config->get('[application][directory]');

        foreach ($files as $key => $file) {
            if (!file_exists($this->pwd . '/' . $file)) {
                throw new \Exception('File does not exist - ' . $file);
            }
        }

        if (!$files) {
            if (file_exists($this->pwd . '/docker-compose.yml')) {
                $files[] = 'docker-compose.yml';
            }
            if (file_exists($this->pwd . '/docker-compose.overrides.yml')) {
                $files[] = 'docker-compose.overrides.yml';
            }
        }

        $this->files = $files;
    }

    /**
     * @return string
     */
    public function getBuildCommand()
    {
        $cmd = $this->config->get('[config][docker_compose_binary]');

        foreach ($this->files as $file) {
            $cmd .= ' -f ' . $file;
        }

        $cmd .= ' build';

        return $cmd;
    }

    /**
     * @param $serviceName
     * @param bool $detached
     * @return string
     */
    public function getRunCommand($serviceName, $detached = true)
    {
        $cmd = $this->config->get('[config][docker_compose_binary]');

        foreach ($this->files as $file) {
            $cmd .= ' -f ' . $file;
        }

        $cmd .= ' run';

        if ($detached) {
            $cmd = $cmd . ' -d';
        }

        $cmd = $cmd . ' ' . $serviceName;

        return $cmd;
    }
}