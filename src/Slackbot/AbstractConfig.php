<?php

namespace Slackbot;

/**
 * Class AbstractConfig
 * @package Slackbot
 */
abstract class AbstractConfig
{
    protected static $configs;

    /**
     * @param       $key
     * @param array $replacements
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($key, $replacements = [])
    {
        if (!array_key_exists($key, static::$configs)) {
            throw new \Exception("Key: '{$key}' does not exist in configs");
        }

        $found = static::$configs[$key];

        if (empty($replacements)) {
            return $found;
        }

        foreach ($replacements as $key => $value) {
            $found = str_replace('{' . $key . '}', $value, $found);
        }

        return $found;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        if (array_key_exists($key, static::$configs)) {
            static::$configs[$key] = $value;
        }
    }
}
