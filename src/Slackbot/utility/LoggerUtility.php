<?php

namespace Slackbot\utility;

/**
 * Class LoggerUtility.
 */
class LoggerUtility extends AbstractUtility
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    private $logFilePath;

    /**
     * @param $function
     * @param string $message
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function logChat($function, $message = '')
    {
        try {
            return $this->logRaw($this->getLogContent($function, $message));
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
        if ($this->getConfig()->get('chatLogging') !== true) {
            return false;
        }

        return true;
    }

    /**
     * Make temp dir IF does not exist.
     *
     * @return bool
     */
    private function makeTmpDir()
    {
        $tmpDir = $this->getTempDir();

        // Directory already exists, return true
        if (is_dir($tmpDir)) {
            return true;
        }

        // dir doesn't exist, make it
        return mkdir($tmpDir);
    }

    /**
     * @param $text
     *
     * @throws \Exception
     *
     * @return bool
     */
    private function write($text)
    {
        try {
            $result = file_put_contents(
                $this->getLogFilePath(),
                $text,
                FILE_APPEND
            );

            if ($result !== false) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            throw new \Exception('Failed to write to the log file');
        }
    }

    /**
     * @param $message
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function logRaw($message)
    {
        try {
            if ($this->canLog() === true && $this->makeTmpDir() === true && $this->write($message) === true) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getTempDir()
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.$this->getConfig()->get('tmpFolderName');
    }

    /**
     * @param $logFilePath
     */
    public function setLogFilePath($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getLogFilePath()
    {
        if (!isset($this->logFilePath)) {
            $logFilePath = $this->getTempDir().DIRECTORY_SEPARATOR.$this->getConfig()->get('chatLoggingFileName');
            $this->setLogFilePath($logFilePath);
        }

        return $this->logFilePath;
    }

    /**
     * @param $function
     * @param $message
     *
     * @return string
     */
    public function getLogContent($function, $message)
    {
        return date(self::DATE_FORMAT)."|{$function}|{$message}\r\n";
    }
}
