<?php
/**
 * Created by PhpStorm.
 * User: ehsan.abbasi
 * Date: 5/12/2016
 * Time: 1:28 PM
 */

namespace Slackbot\utility;

use Slackbot\Config;

class LoggerUtility extends AbstractUtility
{
    /**
     * @param $function
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function logChat($function, $message = '')
    {
        $config = $this->getConfig();
        
        if ($config === null) {
            $config = new Config();    
        }

        if ($config->get('chatLogging') !== true) {
            return false;
        }

        $currentTime = date('Y-m-d H:i:s');

        $tmpDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . $config->get('tmpFolderName');
        if (!is_dir($tmpDir)) {
            // dir doesn't exist, make it
            mkdir($tmpDir);
        }

        file_put_contents(
            $tmpDir . DIRECTORY_SEPARATOR . $config->get('chatLoggingFileName'),
            "{$currentTime}|{$function}|{$message}\r\n",
            FILE_APPEND
        );
        
        return true;
    }
}
