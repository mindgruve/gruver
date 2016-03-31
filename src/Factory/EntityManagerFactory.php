<?php

namespace Mindgruve\Gruver\Factory;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Mindgruve\Gruver\Config\GruverConfig;

class EntityManagerFactory
{
    /**
     * @var GruverConfig
     */
    protected $config;

    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
    }

    public function getEntityManager()
    {
        $config = Setup::createAnnotationMetadataConfiguration(
            array(__DIR__ . '/../Entity'),
            $this->config->get('[config][dev_mode]'),
            $this->config->get('[directories][proxy_dir]')
        );

        return EntityManager::create($this->getDatabaseParams(), $config);
    }

    public function getDatabaseParams()
    {
        $dbParams = $this->config->get('[database]');
        if ($dbParams['driver'] == 'pdo_sqlite' && !isset($dbParams['path'])) {
            $dbParams['path'] = $this->config->get('[directories][data_dir]') . '/data.db';
        }

        return $dbParams;
    }
}
