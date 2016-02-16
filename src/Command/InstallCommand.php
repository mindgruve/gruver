<?php

namespace Mindgruve\Gruver\Command;

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
        $docker = $this->get('docker');
        $dockerCompose = $this->get('docker_compose');
        $sqlite3 = $this->get('sqlite3');

        $output->writeln('Checking gruver dependencies... ');

        /**
         * SQLite3
         * @todo confirm minium version of SQLite
         */
        if ($sqlite3->binaryExists()) {
            $output->writeln('<info>(✓) SQLite3 installed on server</info>');
        } else {
            $output->writeln('<error>(x) SQLite3 not installed on server</error>');
        }

        if (extension_loaded('sqlite3')) {
            $output->writeln('<info>(✓) SQLite extension for PHP loaded</info>');
        } else {
            $output->writeln('<error>(x) SQLite extension for PHP not loaded</error>');
        }
        $version = $sqlite3->getVersion();
        if ((float) ($version['major'].'.'.$version['minor']) < 3.0) {
            $output->writeln('<error>(x) SQLite version < 3.0</error>');
        } else {
            $output->writeln('<info>(✓) SQLite version >= 3.0</info>');
        }

        /**
         * Docker
         * @todo confirm minimum version of SQLite
         */
        if ($docker->binaryExists()) {
            $output->writeln('<info>(✓) Docker installed</info>');
        } else {
            $output->writeln('<error>(x) Docker needs to be installed</error>');
        }

        $version = $docker->getVersion();
        if ((float) ($version['major'].'.'.$version['minor']) < 1.9 && $version['patch'] < 1) {
            $output->writeln('<error>(x) Docker version < 1.9.1</error>');
        } else {
            $output->writeln('<info>(✓) Docker version >= 1.9.1</info>');
        }

        /**
         * Docker Compose
         * @todo confirm minimum version of Docker-Compose
         */
        if ($dockerCompose->binaryExists()) {
            $output->writeln('<info>(✓) Docker-Compose installed</info>');
        } else {
            $output->writeln('<error>(x) Docker-Compose needs to be installed</error>');
        }

        $version = $docker->getVersion();
        if ((float) ($version['major'].'.'.$version['minor']) < 1.6) {
            $output->writeln('<error>(x) Docker-Compose version < 1.6</error>');
        } else {
            $output->writeln('<info>(✓) Docker-Compose version >= 1.6</info>');
        }

        /**
         * Config Directory /etc/gruver
         */
        $process = new Process('mkdir -p /etc/gruver');
        $process->run();
        if (!file_exists('/etc/gruver/config.yml')) {
            copy(__DIR__.'/../Resources/config/gruver.yml', '/etc/gruver/config.yml');
        }
        if (file_exists('/etc/gruver/config.yml')) {
            $output->writeln('<info>(✓) Able to write to /etc/gruver</info>');
        } else {
            $output->writeln('<error>(x) Unable to write to /etc/gruver</error>');
        }

        /**
         * SQLite Database
         */
        $process = new Process('mkdir -p /var/lib/gruver');
        $process->run();

        /**
         * Releases Directory
         */
        $process = new Process('mkdir -p /var/lib/gruver/releases');
        $process->run();
    }
}
