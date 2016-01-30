<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Config\GruverConfig;
use Mindgruve\Gruver\Process\DockerComposeProcess;
use Mindgruve\Gruver\Process\DockerProcess;
use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{

    const COMMAND = 'install';
    const DESCRIPTION = 'Install gruver.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $docker = $this->container['docker'];
        $dockerCompose = $this->container['docker'];

        $output->writeln('Checking gruver dependencies... ');

        /**
         * Docker
         */
        if ($docker->binaryExists()) {
            $output->writeln('<info>(✓) Docker installed</info>');
        } else {
            $output->writeln('<error>(x) Docker needs to be installed</error>');
        }

        $version = $docker->getVersion();
        if ((float)($version['major'] . '.' . $version['minor']) < 1.9) {
            $output->writeln('<error>(x) Docker version < 1.9</error>');
        } else {
            $output->writeln('<info>(✓) Docker version >= 1.9</info>');
        }


        /**
         * Docker Compose
         */
        if ($dockerCompose->binaryExists()) {
            $output->writeln('<info>(✓) Docker-Compose installed</info>');
        } else {
            $output->writeln('<error>(x) Docker-Compose needs to be installed</error>');
        }

        $version = $docker->getVersion();
        if ((float)($version['major'] . '.' . $version['minor']) < 1.4) {
            $output->writeln('<error>(x) Docker-Compose version < 1.4</error>');
        } else {
            $output->writeln('<info>(✓) Docker-Compose version >= 1.4</info>');
        }

        /**
         * Config Directory /etc/gruver
         */
        $process = new Process('mkdir -p /etc/gruver');
        $process->run();
        if (!file_exists('/etc/gruver/gruver.yml')) {
            copy(__DIR__ . '/../Resources/config/gruver.yml', '/etc/gruver/gruver.yml');
        }
        if (file_exists('/etc/gruver/gruver.yml')) {
            $output->writeln('<info>(✓) Able to write to /etc/gruver</info>');
        } else {
            $output->writeln('<error>(x) Unable to write to /etc/gruver</error>');
        }
    }
}