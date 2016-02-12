<?php

namespace Mindgruve\Gruver\Process;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Process\Process;

class DockerProcess
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
        $process = new Process('which '.$this->config->get('[binaries][docker_binary]'));
        $process->run();
        if ($process->getOutput()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getRemoveExitedContainersCommand()
    {
        return $this->config->get('[binaries][docker_binary]').' rm -v $(docker ps -a -q -f status=exited)';
    }

    /**
     * @return string
     */
    public function getRemoveOrphanImagesCommand()
    {
        return $this->config->get('[binaries][docker_binary]').' rmi $(docker images -f "dangling=true" -q)';
    }

    /**
     * @return array|null
     */
    public function getVersion()
    {
        $process = new Process($this->config->get('[binaries][docker_binary]').' --version');
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

        return;
    }
}
