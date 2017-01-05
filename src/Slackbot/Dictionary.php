<?php

namespace Slackbot;

use Slackbot\utility\FileUtility;

/**
 * Class Dictionary.
 */
class Dictionary
{
    const DICTIONARY_DIR = 'dictionary';
    const DICTIONARY_FILE_SUFFIX = 'json';

    private $data;

    /**
     * @param $key
     *
     * @throws \Exception
     *
     * @return array|mixed
     */
    private function load($key)
    {
        $stopWordsPath = __DIR__.DIRECTORY_SEPARATOR.self::DICTIONARY_DIR.DIRECTORY_SEPARATOR.$key.'.'.
            self::DICTIONARY_FILE_SUFFIX;

        return (new FileUtility())->jsonFileToArray($stopWordsPath);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $data = $this->getData();

        if (!isset($data[$key])) {
            $data[$key] = $this->load($key);
            $this->setData($data);
        }

        return $data[$key];
    }
}
