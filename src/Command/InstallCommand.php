<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class InstallCommand extends BaseCommand
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

        /*
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
        if ($version->compareVersion(3.0)) {
            $output->writeln('<error>(x) SQLite version < 3.0</error>');
        } else {
            $output->writeln('<info>(✓) SQLite version >= 3.0</info>');
        }

        /*
         * Docker
         */
        if ($docker->binaryExists()) {
            $output->writeln('<info>(✓) Docker installed</info>');
        } else {
            $output->writeln('<error>(x) Docker needs to be installed</error>');
        }
        $version = $docker->getVersion();
        if ($version->compareVersion(1, 10)) {
            $output->writeln('<error>(x) Docker version < 1.10</error>');
        } else {
            $output->writeln('<info>(✓) Docker version >= 1.10</info>');
        }

        /*
         * Docker Compose
         */
        if ($dockerCompose->binaryExists()) {
            $output->writeln('<info>(✓) Docker-Compose installed</info>');
        } else {
            $output->writeln('<error>(x) Docker-Compose needs to be installed</error>');
        }

        $version = $dockerCompose->getVersion();
        if ($version->compareVersion(1, 6)) {
            $output->writeln('<error>(x) Docker-Compose version < 1.6</error>');
        } else {
            $output->writeln('<info>(✓) Docker-Compose version >= 1.6</info>');
        }

        /*
         * Config Directory /etc/gruver
         */
        $process = new Process('mkdir -p /etc/gruver');
        $process->run();
        if (!file_exists('/etc/gruver/config.yml')) {
            copy(__DIR__ . '/../Resources/config/gruver.yml', '/etc/gruver/config.yml');
        }
        if (file_exists('/etc/gruver/config.yml')) {
            $output->writeln('<info>(✓) Able to write to /etc/gruver</info>');
        } else {
            $output->writeln('<error>(x) Unable to write to /etc/gruver</error>');
        }

        /**
         * Template Directory
         */
        $process = new Process('mkdir -p /etc/gruver/templates');
        $process->run();
        if (!file_exists('/etc/gruver/templates/haproxy.cfg.twig')) {
            copy(__DIR__ . '/../Resources/templates/haproxy.cfg.twig', '/etc/gruver/templates/haproxy.cfg.twig');
        }
        if (file_exists('/etc/gruver/templates/haproxy.cfg.twig')) {
            $output->writeln('<info>(✓) Able to write to /etc/gruver/templates</info>');
        } else {
            $output->writeln('<error>(x) Unable to write to /etc/gruver/templates</error>');
        }

        /*
         * SQLite Database
         */
        $process = new Process('mkdir -p /var/lib/gruver');
        $process->run();

        /*
         * Releases Directory
         */
        $process = new Process('mkdir -p /var/lib/gruver/releases');
        $process->run();

        /**
         * Cache Dir Directory
         */
        $process = new Process('mkdir -p /var/lib/gruver/cache');
        $process->run();
        touch('/var/lib/gruver/cache/temp.txt');
        if(file_exists('/var/lib/gruver/cache/temp.txt')){
            $output->writeln('<info>(✓) Able to write to /var/lib/gruver/cache</info>');
        } else {
            $output->writeln('<error>(x) Unable to write to /var/lib/gruver/templates</error>');
        }

        if(file_exists('/var/lib/gruver/cache/temp.txt')){
            $process = new Process('rm -Rf /var/lib/gruver/cache/*');
            $process->run();

            $command = $this->getApplication()->find('doctrine:generate-proxies');
            $arguments = array();
            $greetInput = new ArrayInput($arguments);
            $returnCode = $command->run($greetInput, new NullOutput());

            if($returnCode === 0){
                $output->writeln('<info>(✓) Able to generate doctrine proxies to /var/lib/gruver/cache</info>');
            } else {
                $output->writeln('<error>(✓) Error generated when generating doctrine proxies.</error>');
            }
        }

        $question = new ConfirmationQuestion('Do you want to update schema? ',false);
        $helper = $this->getHelper('question');
        if($helper->ask($input, $output, $question)){
            echo 'update';
        }


    }
}
