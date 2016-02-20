<?php

namespace Mindgruve\Gruver\Process;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Process\Process;

class DockerProcess implements ProcessInterface
{
    /**
     * @var GruverConfig
     */
    protected $config;

    /**
     * @param GruverConfig $config
     */
    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function binaryExists()
    {
        $process = new Process('which ' . $this->config->get('[binaries][docker_binary]'));
        $process->run();
        if ($process->getOutput() == '') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return string
     */
    public function getRemoveExitedContainersCommand()
    {
        return $this->config->get('[binaries][docker_binary]') . ' rm -v $(docker ps -a -q -f status=exited)';
    }

    /**
     * @return string
     */
    public function getRemoveOrphanImagesCommand()
    {
        return $this->config->get('[binaries][docker_binary]') . ' rmi $(docker images -f "dangling=true" -q)';
    }

    public function getFilterPSContainersCommand($filter, $format = null)
    {
        $cmd = $this->config->get('[binaries][docker_binary]') . ' ps --filter "' . $filter . '"';

        if ($format) {
            $cmd = $cmd . ' --format="' . $format . '"';
        }

        return $cmd;
    }

    public function getContainerPortsByGruverUUIDCommand($uuid)
    {
        return $this->getFilterPSContainersCommand('label=gruver.uuid=' . $uuid, '{{.Ports}}');
    }

    public function getContainerIdByGruverUUIDCommand($uuid)
    {
        return $this->getFilterPSContainersCommand('label=gruver.uuid=' . $uuid, '{{.ID}}');
    }

    /**
     * @return ProcessVersion
     */
    public function getVersion()
    {
        $process = new Process($this->config->get('[binaries][docker_binary]') . ' --version');
        $process->run();
        $version = preg_match('/version ([0-9]+).([0-9]+).([0-9]+)/', trim($process->getOutput()), $matches);
        if ($version) {
            $major = $matches[1];
            $minor = $matches[2];
            $patch = $matches[3];

            return new ProcessVersion($major, $minor, $patch);
        }
    }
}
