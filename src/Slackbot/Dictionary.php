<?php

namespace Slackbot;

use Slackbot\utility\FileUtility;

/**
 * Class Dictionary
 * @package Slackbot
 */
class Dictionary
{
    private $data;

    /**
     * @param $key
     *
     * @return array|mixed
     * @throws \Exception
     */
    private function load($key)
    {
        $stopWordsPath = __DIR__ . DIRECTORY_SEPARATOR . 'dictionary' . DIRECTORY_SEPARATOR . $key . '.json';
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
