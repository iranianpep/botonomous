<?php

namespace Botonomous\utility;

/**
 * Class FileUtility.
 */
class FileUtility extends AbstractUtility
{
    /**
     * Validate and convert a (JSON) file content to a PHP array.
     *
     * @param   $filePath
     *
     * @throws \Exception
     *
     * @return array|mixed
     */
    public function jsonFileToArray($filePath)
    {
        if (empty($filePath)) {
            throw new \Exception('File path is empty');
        }

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new \Exception('File does not exist or is not a file');
        }

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'json') {
            throw new \Exception('File is not a json file');
        }

        return (new StringUtility())->jsonToArray(file_get_contents($filePath));
    }
}
