<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Command extends BaseCommand
{
    protected function runProcess($cmd, GruverConfig $config, $timeout = 3600, OutputInterface $output = null)
    {
        $cmd = $config->getEnvironmentalVariableExport() . ' ' . $cmd;

        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->run(
            function ($type, $buffer) use ($output) {
                if ($output) {
                    $output->write($buffer);
                }
            }
        );
    }

    protected function mustRunProcess($cmd, GruverConfig $config, $timeout = 3600, OutputInterface $output = null)
    {
        $cmd = $config->getEnvironmentalVariableExport() . ' ' . $cmd;

        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->mustRun(
            function ($type, $buffer) use ($output) {
                if ($output) {
                    $output->write($buffer);
                }
            }
        );
    }
}