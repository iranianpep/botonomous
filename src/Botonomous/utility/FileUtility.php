<?php

namespace Botonomous\utility;

/**
 * Class FileUtility.
 */
class FileUtility extends AbstractUtility
{
    const EMPTY_FILE_PATH_MESSAGE = 'File path is empty';
    const MISSING_FILE_MESSAGE = 'File does not exist or is not a file';
    const INVALID_JSON_FILE_MESSAGE = 'File is not a json file';

    /**
     * Validate and convert a (JSON) file content to a PHP array.
     *
     * @param   $filePath
     *
     * @throws \Exception
     *
     * @return array|mixed
     */
    public function jsonFileToArray(string $filePath)
    {
        if (empty($filePath)) {
            throw new \Exception(self::EMPTY_FILE_PATH_MESSAGE);
        }

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new \Exception(self::MISSING_FILE_MESSAGE);
        }

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'json') {
            throw new \Exception(self::INVALID_JSON_FILE_MESSAGE);
        }

        return (new StringUtility())->jsonToArray(file_get_contents($filePath));
    }
}
