<?php

namespace Botonomous;

use Botonomous\utility\ArrayUtility;
use Botonomous\utility\StringUtility;

/**
 * Class AbstractConfig.
 */
abstract class AbstractConfig
{
    protected static $configs;

    /**
     * @param       $key
     * @param array $replacements
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get($key, $replacements = [])
    {
        if (!array_key_exists($key, static::$configs)) {
            throw new \Exception("Key: '{$key}' does not exist in configs");
        }

        $found = static::$configs[$key];

        return (new StringUtility())->applyReplacements($found, $replacements);
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        is_array($key) ? (new ArrayUtility())->setNestedArrayValue(static::$configs, $key, $value)
            : static::$configs[$key] = $value;
    }
}
