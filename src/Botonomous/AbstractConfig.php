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
     * @param null  $plugin
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($key, $replacements = [], $plugin = null)
    {
        if ($plugin !== null) {
            try {
                $configs = $this->getPluginConfigs($plugin);
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            $configs = static::$configs;
        }

        if (!array_key_exists($key, $configs)) {
            throw new \Exception("Key: '{$key}' does not exist in configs");
        }

        $found = $configs[$key];

        return (new StringUtility())->applyReplacements($found, $replacements);
    }

    public function getPluginConfigs($plugin)
    {
        $pluginConfigClass = __NAMESPACE__."\\components\\".strtolower($plugin)
            .'\\'.ucfirst($plugin).'Config';

        if (!class_exists($pluginConfigClass)) {
            throw new \Exception("Config file: '{$pluginConfigClass}.php' does not exist");
        }

        $pluginConfigObject = new $pluginConfigClass();
        if (!$pluginConfigObject instanceof self) {
            throw new \Exception("Class: '{$pluginConfigClass}' must extend BaseConfig");
        }

        return $pluginConfigObject->getConfigs();
    }

    public function getConfigs()
    {
        return static::$configs;
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
