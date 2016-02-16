<?php

namespace Mindgruve\Gruver\Process;

use Mindgruve\Gruver\Config\EnvironmentalVariables;
use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Process\Process;
use Webpatser\Uuid\Uuid;

class DockerComposeProcess
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
     * @var EnvironmentalVariables
     */
    protected $env;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param GruverConfig $config
     *
     * @throws \Exception
     */
    public function __construct(GruverConfig $config, EnvironmentalVariables $env, \Twig_Environment $twig)
    {
        $this->config = $config;
        $this->env = $env;
        $this->twig = $twig;

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

        $uuid = Uuid::generate();
        $releaseFile = '/var/lib/gruver/releases/' . $uuid . '.yml';
        $contents = $this->twig->render(
            'docker-compose.yml.twig',
            array(
                'project_name' => $env->getProjectName(),
                'service_name' => $env->getServiceName(),
                'release' => $env->getRelease(),
                'uuid' => $uuid
            )
        );
        file_put_contents($releaseFile, $contents);
        $files[] = $releaseFile;

        $this->files = $files;
    }

    public function binaryExists()
    {
        $process = new Process('which ' . $this->config->get('[binaries][docker_compose_binary]'));
        $process->run();
        if ($process->getOutput()) {
            return true;
        } else {
            return false;
        }
    }

    public function getVersion()
    {
        $process = new Process($this->config->get('[binaries][docker_compose_binary]') . ' --version');
        $process->run();
        $version = preg_match('/version ([0-9]).([0-9]).([0-9])/', trim($process->getOutput()), $matches);
        if ($version) {
            $dockerMajorVersion = $matches[1];
            $dockerMinorVersion = $matches[2];
            $dockerPatch = $matches[3];

            return array(
                'major' => $dockerMajorVersion,
                'minor' => $dockerMinorVersion,
                'patch' => $dockerPatch,
            );
        }

        return array();
    }

    /**
     * @return string
     */
    public function getBuildCommand()
    {
        $cmd = $this->config->get('[binaries][docker_compose_binary]');
        $cmd = $this->env->buildExport() . ' ' . $cmd;

        foreach ($this->files as $file) {
            $cmd .= ' -f ' . $file;
        }

        $cmd .= ' build';

        return $cmd;
    }

    /**
     * @param $serviceName
     * @param bool $detached
     *
     * @return string
     */
    public function getUpCommand($serviceName, $detached = true)
    {
        return $this->getRunCommand($serviceName, $detached);
        $cmd = $this->config->get('[binaries][docker_compose_binary]');
        $cmd = $this->env->buildExport() . ' ' . $cmd;

        foreach ($this->files as $file) {
            $cmd .= ' -f ' . $file;
        }

        $cmd .= ' up';

        if ($detached) {
            $cmd = $cmd . ' -d';
        }

        if ($serviceName) {
            $cmd = $cmd . ' ' . $serviceName;
        }

        return $cmd;
    }

    /**
     * @param $serviceName
     * @param bool $detached
     *
     * @return string
     */
    public function getRunCommand($serviceName, $detached = true, $servicePorts = true)
    {
        $cmd = $this->config->get('[binaries][docker_compose_binary]');
        $cmd = $this->env->buildExport() . ' ' . $cmd;

        foreach ($this->files as $file) {
            $cmd .= ' -f ' . $file;
        }

        $cmd .= ' run';

        if ($detached) {
            $cmd = $cmd . ' -d';
        }

        if($servicePorts){
            $cmd = $cmd .' --service-ports';
        }

        if ($serviceName) {
            $cmd = $cmd . ' ' . $serviceName;
        }

        return $cmd;
    }
}
