<?php

namespace Mindgruve\Gruver\Factory;

use Mindgruve\Gruver\Handler\ConsoleOutputHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Console\Output\OutputInterface;

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

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(GruverConfig $config, OutputInterface $output)
    {
        $this->defaultLogDirectory = $config->get('[directories][logging_dir]');
        $this->output = $output;

        $adapterConfigs = $config->get('[logging][adapters]');
        foreach ($adapterConfigs as $key => $loggingConfig) {
            if (isset($loggingConfig['type']) && $loggingConfig['type'] == 'stream' && !isset($loggingConfig['path'])) {
                $adapterConfigs[$key]['path'] = $config->get('[directories][logging_dir]') . '/' . $key . '.log';
            }
        }

        $logger = new Logger('default');
        foreach ($adapterConfigs as $adapterConfig) {
            $this->addAdapter($logger, $adapterConfig, $this->defaultLogDirectory . '/default.log');
        }
        $logger->pushHandler(new ConsoleOutputHandler($this->output));

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
