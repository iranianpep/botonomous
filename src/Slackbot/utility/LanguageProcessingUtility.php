<?php
/**
 * Created by PhpStorm.
 * User: ehsan.abbasi
 * Date: 7/12/2016
 * Time: 9:23 AM
 */

namespace Slackbot\utility;

class LanguageProcessingUtility
{
    /**
     * @param $text
     * @return string
     */
    public function stem($text)
    {
        // Execute the python script with the JSON data
        $filePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'py' . DIRECTORY_SEPARATOR . 'stemmer.py';

        //return exec('python ' . $filePath . ' ' . escapeshellarg(json_encode([$text])), $output);
        return shell_exec('python ' . $filePath . ' ' . escapeshellarg(json_encode([$text])));
    }
}
