<?php

namespace Mindgruve\Gruver\Command;

use Doctrine\DBAL\DriverManager;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends BaseCommand
{
    const COMMAND = 'install';
    const DESCRIPTION = 'Install gruver.';

    const COMMAND_FAIL = 1;
    const COMMAND_SUCCESS = 0;

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->checkDependencies() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        if ($this->createDirectories() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }
        if ($this->generateProxies() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        if ($this->createDatabase() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        $this->get('logger')->addDebug(PHP_EOL . 'Installation Complete!');
    }

    public function checkDependencies()
    {
        $docker = $this->get('docker');
        $dockerCompose = $this->get('docker_compose');
        $sqlite3 = $this->get('sqlite3');
        $logger = $this->get('logger');

        $logger->addDebug(PHP_EOL . 'Checking gruver dependencies... ');

        /**
         * SQLite3
         */
        if ($sqlite3->binaryExists()) {
            $logger->addInfo('(✓) SQLite3 installed on server');
        } else {
            $logger->addError('(x) SQLite3 not installed on server');

            return self::COMMAND_FAIL;
        }

        if (extension_loaded('sqlite3')) {
            $logger->addInfo('(✓) SQLite extension for PHP loaded');
        } else {
            $logger->addError('(x) SQLite extension for PHP not loaded');

            return self::COMMAND_FAIL;
        }
        $version = $sqlite3->getVersion();
        if ($version->compareVersion(3.0)) {
            $logger->addError('(x) SQLite version < 3.0');

            return self::COMMAND_FAIL;
        } else {
            $logger->addInfo('(✓) SQLite version >= 3.0');
        }

        /**
         * Docker
         */
        if ($docker->binaryExists()) {
            $logger->addInfo('(✓) Docker installed');
        } else {
            $logger->addError('(x) Docker needs to be installed');

            return self::COMMAND_FAIL;
        }
        $version = $docker->getVersion();
        if ($version->compareVersion(1, 10)) {
            $logger->addError('(x) Docker version < 1.10');

            return self::COMMAND_FAIL;
        } else {
            $logger->addInfo('(✓) Docker version >= 1.10');
        }

        /**
         * Docker Compose
         */
        if ($dockerCompose->binaryExists()) {
            $logger->addInfo('(✓) Docker-Compose installed');
        } else {
            $logger->addError('(x) Docker-Compose needs to be installed');

            return self::COMMAND_FAIL;
        }

        $version = $dockerCompose->getVersion();
        if ($version->compareVersion(1, 6)) {
            $logger->addError('(x) Docker-Compose version < 1.6');

            return self::COMMAND_FAIL;
        } else {
            $logger->addInfo('(✓) Docker-Compose version >= 1.6');
        }

        return self::COMMAND_SUCCESS;
    }

    public function createDirectories()
    {
        $fs = $this->get('file_system');
        $logger = $this->get('logger');

        $logger->addDebug(PHP_EOL . 'Creating Gruver Directories ... ');

        /**
         * Get Configuration for Directories
         */
        $config = $this->get('config');

        $configDir = $config->get('[directories][config_dir]');
        if (!$configDir) {
            $logger->addError('Configuration value directories.config_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $cacheDir = $config->get('[directories][cache_dir]');
        if (!$cacheDir) {
            $logger->addError('Configuration value directories.cache_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $dataDir = $config->get('[directories][data_dir]');
        if (!$dataDir) {
            $logger->addError('Configuration value directories.data_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $releasesDir = $config->get('[directories][releases_dir]');
        if (!$releasesDir) {
            $logger->addError('Configuration value directories.releases_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $templatesDir = $config->get('[directories][templates_dir]');
        if (!$templatesDir) {
            $logger->addError('Configuration value directories.templates_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $loggingDir = $config->get('[directories][logging_dir]');
        if (!$loggingDir) {
            $logger->addError('Configuration value directories.logging_dir does not exist');

            return self::COMMAND_FAIL;
        }

        /**
         * Create Directories
         */
        $fs->mkdir($configDir);
        $fs->mkdir($templatesDir);
        $fs->mkdir($dataDir);
        $fs->mkdir($releasesDir);
        $fs->mkdir($cacheDir);
        $fs->mkdir($loggingDir);

        /**
         * Config Directory /etc/gruver
         */
        if (!$fs->exists($configDir . '/config.yml')) {
            $fs->copy(__DIR__ . '/../Resources/config/gruver.yml', $configDir . '/config.yml');
            $logger->addInfo('(✓) Gruver configuration copied to ' . $configDir);
        }

        /**
         * Template Directory
         */
        if ($fs->exists($templatesDir . '/haproxy.cfg.twig')) {
            $fs->copy(__DIR__ . '/../Resources/templates/haproxy.cfg.twig', $templatesDir . '/haproxy.cfg.twig');
            $logger->addInfo('(✓) HAProxy Template copied to ' . $templatesDir);
        }

        /**
         * Clearing Cache Directory
         */
        $fs->remove($cacheDir . '/*');
        $logger->addInfo('(✓) Complete');

        return self::COMMAND_SUCCESS;
    }

    public function generateProxies()
    {
        $logger = $this->get('logger');
        $logger->addDebug(PHP_EOL . 'Generating Doctrine Proxies ... ');
        $command = $this->getApplication()->find('doctrine:orm:generate-proxies');
        $arguments = array();
        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, new NullOutput());

        if ($returnCode === 0) {
            $logger->addInfo('(✓) Able to generate doctrine proxies.');
        } else {
            $logger->addError('(x) Error generated when generating doctrine proxies.');

            return self::COMMAND_FAIL;
        }

        return self::COMMAND_SUCCESS;
    }

    public function createDatabase()
    {
        $params = $this->get('db_params');
        $logger = $this->get('logger');

        $error = false;
        $logger->addDebug(PHP_EOL . 'Generating Database ... ');
        $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

        if (!$name) {
            throw new \InvalidArgumentException(
                "Connection does not contain a 'path' or 'dbname' parameter and cannot be dropped."
            );
        }
        unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);

        if (!isset($params['path'])) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }

        try {
            $tmpConnection->getSchemaManager()->createDatabase($name);
            $logger->addInfo('(✓) Database created or already exists');
        } catch (\Exception $e) {
            $logger->addError(
                sprintf(
                    '(x) Could not create database for connection named <comment>%s</comment>',
                    $name
                )
            );
            $logger->addError(sprintf('%s', $e->getMessage()));
            $error = true;
        }

        $tmpConnection->close();

        return $error ? self::COMMAND_FAIL : self::COMMAND_SUCCESS;
    }
}
