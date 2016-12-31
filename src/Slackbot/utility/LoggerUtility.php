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

        $makeDirResult = $this->makeTmpDir();
        $writeResult = $this->write($this->getLogContent($function, $message));

        if ($makeDirResult === true && $writeResult === true) {
            return true;
        }

        return false;
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
     * @return bool
     */
    private function write($text)
    {
        $result = file_put_contents(
            $this->getLogFilePath(),
            $text,
            FILE_APPEND
        );

        if ($result !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param $message
     *
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
