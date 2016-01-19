<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{

    const COMMAND = 'build';
    const DESCRIPTION = 'Build a docker container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $gruverYaml = $_SERVER['PWD'].'/gruver.yml';
        if (!file_exists($gruverYaml)) {
            $output->write('<error>Gruver could not find a gruver.yml.</error>');
        }
        $config = new GruverConfig($gruverYaml);

        $output->writeln('<info>GRUVER: Building container...</info>');
        $process = new Process($config->get('build.compose_binary'). ' build');
        $process->setTimeout(3600);

        try{
            $process->mustRun(function ($type, $buffer) {
                echo $buffer;
            });
        }catch(\Exception $e){
            echo $e->getMessage();exit;
        }
    }
}