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
        $paths = array(__DIR__.'/../Entity');
        $isDevMode = false;

        // the connection configuration
        $dbParams = array(
            'driver' => $this->config->get('[database][driver]'),
            'user' => $this->config->get('[database][user]'),
            'password' => $this->config->get('[database][password]'),
            'path' => $this->config->get('[database][path]'),
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $entityManager = EntityManager::create($dbParams, $config);

        return $entityManager;
    }
}
