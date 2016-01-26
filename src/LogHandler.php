<?php

namespace Mindgruve\Gruver;

use Mindgruve\Gruver\Config\GruverConfig;
use Monolog\ErrorHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

class LogHandler
{
    /**
     * @var \Monolog\Handler\HandlerInterface
     */
    protected $deploymentLogHandler;

    /**
     * @var \Monolog\Handler\HandlerInterface
     */
    protected $errorLogHandler;

    public function __construct(GruverConfig $config)
    {
        /**
         * Error Log Handler
         */
        $errorLogConfig = $config->get('[config][log_adapter][error]');
        $type = $errorLogConfig['type'];
        $level = $errorLogConfig['level'];
        $path = isset($errorLogConfig['path'])
            ? $errorLogConfig['type']
            : $config->getLogDir() . '/error.log';

        if ($type == 'stream') {
            $errorLogHandler = new StreamHandler($path, $level);
        } elseif ($type == 'rotating_file') {
            $errorLogHandler = new RotatingFileHandler($path, 5, $level);
        } elseif ($type == 'syslog') {
            $errorLogHandler = new SyslogHandler('gruver', 'gruver', $level);
        } elseif ($type == 'error') {
            $errorLogHandler = new ErrorHandler();
        } else {
            throw new \Exception('Uknown log handler type - ' . $type);
        }

        $this->errorLogHandler = $errorLogHandler;

        /**
         * Deployment Log Handler
         */
        $deploymentLogConfig = $config->get('[config][log_adapter][deployment]');
        $type = $deploymentLogConfig['type'];
        $level = $deploymentLogConfig['level'];
        $path = isset($deploymentLogConfig['path'])
            ? $deploymentLogConfig['type']
            : $config->getLogDir() . '/deployment.log';


        if ($type == 'stream') {
            $deploymentLogHandler = new StreamHandler($path, $level);
        } elseif ($type == 'rotating_file') {
            $deploymentLogHandler = new RotatingFileHandler($path, 5, $level);
        } elseif ($type == 'syslog') {
            $deploymentLogHandler = new SyslogHandler('gruver', 'gruver', $level);
        } elseif ($type == 'error') {
            $deploymentLogHandler = new ErrorHandler();
        } else {
            throw new \Exception('Uknown log handler type - ' . $type);
        }
        $this->deploymentLogHandler = $deploymentLogHandler;
    }

    /**
     * @return \Monolog\Handler\HandlerInterface
     */
    public function getErrorLogHandler()
    {
        return $this->errorLogHandler;
    }

    /**
     * @return \Monolog\Handler\HandlerInterface
     */
    public function getDeploymentLogHandler()
    {
        return $this->deploymentLogHandler;
    }
}