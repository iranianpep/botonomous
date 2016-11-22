<?php

namespace Slackbot;

abstract class AbstractConfig
{
    protected static $configs;

    public function get($key)
    {
        if (array_key_exists($key, static::$configs)) {
            return static::$configs[$key];
        } else {
            throw new \Exception("Key: '{$key}' does not exist in configs");
        }
    }
}
