<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class EventDispatcher
{
    /**
     * @var GruverConfig
     */
    protected $config;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param GruverConfig $config
     * @param OutputInterface $output
     */
    public function __construct(GruverConfig $config, OutputInterface $output)
    {
        $this->config = $config;
        $this->output = $output;
    }

    public function dispatchPreBuild()
    {
        $shellCommands = $this->config->get('[events][pre_build]');

        if(!is_array($shellCommands)){
            return;
        }

        foreach ($shellCommands as $shellCommand) {
            if ($shellCommand) {
                $this->runProcess($shellCommand);
            }
        }
    }

    public function dispatchPostBuild()
    {
        $shellCommands = $this->config->get('[events][post_build]');

        if(!is_array($shellCommands)){
            return;
        }

        foreach ($shellCommands as $shellCommand) {
            if ($shellCommand) {
                $this->runProcess($shellCommand);
            }
        }
    }

    public function dispatchPreRun()
    {
        $shellCommands = $this->config->get('[events][pre_run]');

        if(!is_array($shellCommands)){
            return;
        }

        foreach ($shellCommands as $shellCommand) {
            if ($shellCommand) {
                $this->runProcess($shellCommand);
            }
        }
    }

    public function dispatchPostRun()
    {
        $shellCommands = $this->config->get('[events][post_run]');

        if(!is_array($shellCommands)){
            return;
        }

        foreach ($shellCommands as $shellCommand) {
            if ($shellCommand) {
                $this->runProcess($shellCommand);
            }
        }
    }

    public function dispatchPreCleanup()
    {
        $shellCommands = $this->config->get('[events][pre_cleanup]');

        if(!is_array($shellCommands)){
            return;
        }

        foreach ($shellCommands as $shellCommand) {
            if ($shellCommand) {
                $this->runProcess($shellCommand);
            }
        }
    }

    public function dispatchPostCleanup()
    {
        $shellCommands = $this->config->get('[events][post_cleanup]');

        if(!is_array($shellCommands)){
            return;
        }

        foreach ($shellCommands as $shellCommand) {
            if ($shellCommand) {
                $this->runProcess($shellCommand);
            }
        }
    }

    protected function runProcess($shellCommand)
    {
        $output = $this->output;
        $process = new Process($shellCommand);
        $process->setTimeout(3600);
        $process->run(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );
    }
}
