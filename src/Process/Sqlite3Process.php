<?php

namespace Mindgruve\Gruver\Process;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Process\Process;

class Sqlite3Process
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
        $process = new Process('which ' . $this->config->get('[config][sqlite3_binary]'));
        $process->run();
        if ($process->getOutput()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array|null
     */
    public function getVersion()
    {
        $process = new Process($this->config->get('[config][sqlite3_binary]') . ' --version');
        $process->run();
        $version = preg_match('/([0-9]).([0-9]).([0-9])/', trim($process->getOutput()), $matches);
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

        return null;
    }
}