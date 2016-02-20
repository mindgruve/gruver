<?php

namespace Mindgruve\Gruver\Process;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Process\Process;

class Sqlite3Process implements ProcessInterface
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
        $process = new Process('which ' . $this->config->get('[binaries][sqlite3_binary]'));
        $process->run();
        if ($process->getOutput() == '') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return ProcessVersion
     */
    public function getVersion()
    {
        $process = new Process($this->config->get('[binaries][sqlite3_binary]') . ' --version');
        $process->run();
        $version = preg_match('/([0-9]).([0-9]).([0-9])/', trim($process->getOutput()), $matches);
        if ($version) {
            $major = $matches[1];
            $minor = $matches[2];
            $patch = $matches[3];

            return new ProcessVersion($major, $minor, $patch);
        }
    }
}
