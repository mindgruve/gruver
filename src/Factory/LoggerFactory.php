<?php

namespace Mindgruve\Gruver\Factory;

use Monolog\ErrorHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Mindgruve\Gruver\Config\GruverConfig;

class LoggerFactory
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $defaultLogDirectory;

    public function __construct(GruverConfig $config)
    {
        $this->defaultLogDirectory = $config->get('[config][default_log_directory]');

        $adapterConfigs = $config->get('[config][logging][adapters]');
        $logger = new Logger('default');

        foreach ($adapterConfigs as $adapterConfig) {
            $this->addAdapter($logger, $adapterConfig, $this->defaultLogDirectory . '/default.log');
        }

        $this->logger = $logger;
    }

    public function addAdapter(Logger $logger, array $adapterConfig, $defaultPath)
    {
        $type = $adapterConfig['type'];
        $level = $adapterConfig['level'];
        $path = isset($adapterConfig['path']) ? $adapterConfig['path'] : $defaultPath;

        if ($type == 'stream') {
            $logger->pushHandler(new StreamHandler($path, $level));
        } elseif ($type == 'rotating_file') {
            $logger->pushHandler(new StreamHandler($path, 5, $level));
        } elseif ($type == 'syslog') {
            $logger->pushHandler(new SyslogHandler('gruver', 'gruver', $level));
        } elseif ($type == 'error') {
            $logger->pushHandler(new ErrorHandler());
        } else {
            throw new \Exception('Unknown log handler type - ' . $type);
        }

        return $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}