<?php

namespace Slackbot\utility;

/**
 * Class LoggerUtility.
 */
class LoggerUtility extends AbstractUtility
{
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
        $config = $this->getConfig();

        if ($config->get('chatLogging') !== true) {
            return false;
        }

        $tmpDir = $this->getTempDir();
        if (!is_dir($tmpDir)) {
            // dir doesn't exist, make it
            mkdir($tmpDir);
        }

        file_put_contents(
            $this->getLogFilePath(),
            $this->getLogContent($function, $message),
            FILE_APPEND
        );

        return true;
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
     * @throws \Exception
     *
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->getTempDir().DIRECTORY_SEPARATOR.$this->getConfig()->get('chatLoggingFileName');
    }

    /**
     * @param $function
     * @param $message
     *
     * @return string
     */
    public function getLogContent($function, $message)
    {
        return date('Y-m-d H:i:s')."|{$function}|{$message}\r\n";
    }
}
