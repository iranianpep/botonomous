<?php

namespace Slackbot\utility;

class FileUtility
{
    /**
     * Validate and convert a (JSON) file content to a PHP array
     *
     * @param      $filePath
     * @param bool $checkFileType
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function jsonFileToArray($filePath, $checkFileType = false)
    {
        if (empty($filePath)) {
            throw new \Exception('File path is empty');
        }

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new \Exception("File: '{$filePath}' does not exist or is not a file");
        }

        if ($checkFileType === true && pathinfo($filePath, PATHINFO_EXTENSION) !== 'json') {
            throw new \Exception("File: '{$filePath}' is not a json file");
        }

        $content = file_get_contents($filePath);

        return (new StringUtility())->jsonToArray($content);
    }
}
