<?php

namespace Mindgruve\Gruver\Command;

use Doctrine\DBAL\DriverManager;
use Mindgruve\Gruver\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
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

        if ($this->copyConfigs() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        if ($this->generateProxies() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        if ($this->createDatabase() == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        if ($this->runMigrations($input, $output) == self::COMMAND_FAIL) {
            return self::COMMAND_FAIL;
        }

        $this->get('logger')->addDebug(PHP_EOL.PHP_EOL.'Installation Complete!'.PHP_EOL);
    }

    public function checkDependencies()
    {
        $docker = $this->get('docker');
        $dockerCompose = $this->get('docker_compose');
        $sqlite3 = $this->get('sqlite3');
        $logger = $this->get('logger');

        $logger->addDebug(PHP_EOL.'Checking gruver dependencies... ');

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

        $logger->addDebug(PHP_EOL.'Gruver Directories ... ');

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

        $migrationsDir = $config->get('[directories][migrations_dir]');
        if (!$migrationsDir) {
            $logger->addError('Configuration value directories.migrations_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $loggingDir = $config->get('[directories][logging_dir]');
        if (!$loggingDir) {
            $logger->addError('Configuration value directories.logging_dir does not exist');

            return self::COMMAND_FAIL;
        }

        $proxyDir = $config->get('[directories][proxy_dir]');
        if (!$proxyDir) {
            $logger->addError('Configuration value directories.proxy_dir does not exist');

            return self::COMMAND_FAIL;
        }

        /**
         * Create Directories
         */
        $fs->mkdir($configDir);
        $fs->mkdir($templatesDir);
        $fs->mkdir($dataDir);
        $fs->mkdir($releasesDir);
        $fs->mkdir($migrationsDir);
        $fs->mkdir($cacheDir);
        $fs->mkdir($loggingDir);
        $fs->mkdir($proxyDir);

        /**
         * Clearing Cache Directory
         */
        $fs->remove($cacheDir.'/*');
        $logger->addInfo('(✓) Complete');

        return self::COMMAND_SUCCESS;
    }

    public function copyConfigs()
    {

        $fs = $this->get('file_system');
        $logger = $this->get('logger');
        $config = $this->get('config');

        $logger->addDebug(PHP_EOL.'Copying Gruver Configs ... ');
        $configDir = $config->get('[directories][config_dir]');
        $migrationsDir = $config->get('[directories][migrations_dir]');
        $templatesDir = $config->get('[directories][templates_dir]');

        /**
         * Copy Gruver Config
         */
        if (!$fs->exists($configDir.'/config.yml')) {
            $fs->copy(__DIR__.'/../Resources/config/gruver.yml', $configDir.'/config.yml');
            $logger->addInfo('(✓) Gruver configuration copied to '.$configDir);
        }

        /**
         * Copy Migrations Config
         */

        $twig = $this->get('twig');
        if (!$fs->exists($configDir.'/migrations.yml')) {
            $fs->dumpfile(
                $configDir.'/migrations.yml',
                $twig->render('migrations.yml.twig', array('migration_directory' => $migrationsDir))
            );
            $logger->addInfo('(✓) Doctrine Migration Config copied to '.$configDir);
        }

        /**
         * Copy Migrations
         */
        $migrations = array(
            'Version20160324002221.php',
            'Version20160524222904.php',
            'Version20160601201439.php',
        );

        foreach($migrations as $migration){
            if (!$fs->exists($migrationsDir.'/'.$migration)) {
                $fs->copy(
                    __DIR__.'/../Migration/'.$migration,
                    $migrationsDir.'/'.$migration
                );
                $logger->addInfo('(✓) Migration '.preg_replace('/(Version|\.php)/',$migration,'').' copied to '.$configDir);
            }
        }

        /**
         * Copy HAProxy Config
         */
        if ($fs->exists($templatesDir.'/haproxy.cfg.twig')) {
            $fs->copy(__DIR__.'/../Resources/templates/haproxy.cfg.twig', $templatesDir.'/haproxy.cfg.twig');
            $logger->addInfo('(✓) HAProxy Template copied to '.$templatesDir);
        }

        $logger->addInfo('(✓) Complete.');

        return self::COMMAND_SUCCESS;
    }


    public function generateProxies()
    {
        $logger = $this->get('logger');
        $logger->addDebug(PHP_EOL.'Doctrine Proxies ... ');
        $fs = $this->get('file_system');
        $config = $this->get('config');
        $proxyDir = $config->get('[directories][proxy_dir]');

        try {
            $fs->copy(
                __DIR__.'/../Proxies/__CG__MindgruveGruverEntityProject.php',
                $proxyDir.'/__CG__MindgruveGruverEntityProject.php'
            );
            $fs->copy(
                __DIR__.'/../Proxies/__CG__MindgruveGruverEntityRelease.php',
                $proxyDir.'/__CG__MindgruveGruverEntityRelease.php'
            );
            $fs->copy(
                __DIR__.'/../Proxies/__CG__MindgruveGruverEntityService.php',
                $proxyDir.'/__CG__MindgruveGruverEntityService.php'
            );
        } catch (\Exception $e) {
            return self::COMMAND_FAIL;
        }

        $logger->addInfo('(✓) Complete.');

        return self::COMMAND_SUCCESS;

    }

    public function createDatabase()
    {
        $params = $this->get('db_params');
        $logger = $this->get('logger');

        $error = false;
        $logger->addDebug(PHP_EOL.'Generating Database ... ');
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

    public function runMigrations(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->get('logger');

        $logger->addDebug(PHP_EOL.'Database Migrations ... ');

        $cmd = $this->getApplication()->find('doctrine:migrations:status');
        $bufferedOutput = new BufferedOutput();
        $cmd->run(new ArrayInput(array()), $bufferedOutput);

        if (preg_match('/New Migrations:\s*(\d*)/', $bufferedOutput->fetch(), $matches)) {

            if ($matches[1] == 0) {
                $logger->addInfo('(✓) No migrations.');

                return self::COMMAND_SUCCESS;
            }

            $cmd = $this->getApplication()->find('doctrine:migrations:migrate');

            return $cmd->run(new ArrayInput(array('-–no–interaction')), $output);
        }

        $logger->addInfo('(✓) No migrations.');

        return self::COMMAND_SUCCESS;
    }
}
