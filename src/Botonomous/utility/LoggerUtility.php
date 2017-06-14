<?php

namespace Botonomous\utility;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerUtility.
 */
class LoggerUtility extends AbstractUtility
{
    const DATE_FORMAT = 'Y-m-d H:i:s';
    const TEMP_FOLDER = 'tmp';

    const DEBUG = 'debug';
    const INFO = 'info';
    const NOTICE = 'notice';
    const WARNING = 'warning';
    const ERROR = 'error';
    const CRITICAL = 'critical';
    const ALERT = 'alert';
    const EMERGENCY = 'emergency';

    public static $levels = [
        self::DEBUG,
        self::INFO,
        self::NOTICE,
        self::WARNING,
        self::ERROR,
        self::CRITICAL,
        self::ALERT,
        self::EMERGENCY,
    ];

    private $logger;

    /**
     * LoggerUtility constructor.
     *
     * @param null $config
     *
     * @throws \Exception
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        try {
            $this->initLogger();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Init the logger.
     */
    private function initLogger()
    {
        $monologConfig = $this->getMonologConfig();

        if (empty($monologConfig)) {
            throw new \Exception('Monolog config is missing');
        }

        $logger = new Logger($monologConfig['channel']);

        foreach (array_keys($monologConfig['handlers']) as $value) {
            $logger = $this->pushMonologHandler($logger, $value);
        }

        $this->setLogger($logger);
    }

    /**
     * @return mixed
     */
    private function getMonologConfig()
    {
        $loggerConfig = $this->getConfig()->get('logger');

        return !empty($loggerConfig['monolog']) ? $loggerConfig['monolog'] : false;
    }

    /**
     * @param Logger $logger
     * @param        $handlerKey
     *
     * @return Logger
     */
    private function pushMonologHandler(Logger $logger, $handlerKey)
    {
        $activeHandlers = [];

        switch ($handlerKey) {
            case 'file':
                $activeHandlers[] = new StreamHandler($this->getLogFilePath());
                break;
        }

        if (!empty($activeHandlers)) {
            foreach ($activeHandlers as $activeHandler) {
                $logger->pushHandler($activeHandler);
            }
        }

        return $logger;
    }

    /**
     * @return bool|string
     */
    public function getLogFilePath()
    {
        $monologConfig = $this->getMonologConfig();

        if (!isset($monologConfig['handlers']['file']['fileName'])) {
            return false;
        }

        return $this->getTempDir().DIRECTORY_SEPARATOR.$monologConfig['handlers']['file']['fileName'];
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    private function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $function
     * @param string $message
     * @param string $channel
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function logChat($function, $message = '', $channel = '')
    {
        try {
            return $this->logInfo('Log Chat', [
                'function' => $function,
                'message'  => $message,
                'channel'  => $channel,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    private function canLog()
    {
        $loggerConfig = $this->getConfig()->get('logger');

        return empty($loggerConfig['enabled']) ? false : true;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getTempDir()
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.self::TEMP_FOLDER;
    }

    /**
     * @param $function
     * @param string $message
     * @param $channel
     *
     * @return string
     */
    public function getLogContent($function, $message, $channel)
    {
        return "{$function}|{$message}|{$channel}";
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logDebug($message, array $context = [])
    {
        return $this->log(self::DEBUG, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logInfo($message, array $context = [])
    {
        return $this->log(self::INFO, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logNotice($message, array $context = [])
    {
        return $this->log(self::NOTICE, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logWarning($message, array $context = [])
    {
        return $this->log(self::WARNING, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logError($message, array $context = [])
    {
        return $this->log(self::ERROR, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logCritical($message, array $context = [])
    {
        return $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logAlert($message, array $context = [])
    {
        return $this->log(self::ALERT, $message, $context);
    }

    /**
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function logEmergency($message, array $context = [])
    {
        return $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function log($level, $message, $context = [])
    {
        if ($this->canLog() !== true) {
            return false;
        }

        $logger = $this->getLogger();

        if (!in_array($level, self::$levels)) {
            throw new \Exception("'{$level}' is an invalid log level");
        }

        $logger->$level($message, $context);

        return true;
    }
}
