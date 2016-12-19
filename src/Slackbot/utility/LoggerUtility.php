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
        if ($this->canLog() !== true) {
            return false;
        }

        $this->makeTmpDir();
        $this->write($this->getLogContent($function, $message));

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function canLog()
    {
        if ($this->getConfig()->get('chatLogging') !== true) {
            return false;
        }

        return true;
    }

    /**
     * Make temp dir IF does not exist
     */
    private function makeTmpDir()
    {
        $tmpDir = $this->getTempDir();
        if (!is_dir($tmpDir)) {
            // dir doesn't exist, make it
            mkdir($tmpDir);
        }
    }

    /**
     * @param $text
     */
    private function write($text)
    {
        file_put_contents(
            $this->getLogFilePath(),
            $text,
            FILE_APPEND
        );
    }

    /**
     * @param $message
     * @return bool
     */
    public function logRaw($message)
    {
        if ($this->canLog() !== true) {
            return false;
        }

        $this->makeTmpDir();
        $this->write($message);

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
