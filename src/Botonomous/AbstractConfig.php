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
     * @throws \Exception
     *
     * @return mixed
     */
    public function get($key, $replacements = [], $plugin = null)
    {
        $configs = static::$configs;

        // overwrite $configs if $plugin is presented
        if ($plugin !== null) {
            try {
                $configs = $this->getPluginConfigs($plugin);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if (!array_key_exists($key, $configs)) {
            throw new \Exception("Key: '{$key}' does not exist in configs");
        }

        $found = $configs[$key];

        return (new StringUtility())->applyReplacements($found, $replacements);
    }

    /**
     * @param $plugin
     *
     * @return self
     * @throws \Exception
     */
    private function getPluginConfigObject($plugin)
    {
        $pluginConfigClass = __NAMESPACE__.'\\plugin\\'.strtolower($plugin)
            .'\\'.ucfirst($plugin).'Config';

        if (!class_exists($pluginConfigClass)) {
            throw new \Exception("Config file: '{$pluginConfigClass}.php' does not exist");
        }

        return new $pluginConfigClass();
    }

    /**
     * @param $plugin
     *
     * @return array
     * @throws \Exception
     */
    public function getPluginConfigs($plugin)
    {
        try {
            return $this->getPluginConfigObject($plugin)->getConfigs();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return static::$configs;
    }

    /**
     * @param      $key
     * @param      $value
     * @param null $plugin
     *
     * @throws \Exception
     */
    public function set($key, $value, $plugin = null)
    {
        if ($plugin !== null) {
            try {
                $this->getPluginConfigObject($plugin)->set($key, $value);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        is_array($key) ? (new ArrayUtility())->setNestedArrayValue(static::$configs, $key, $value)
            : static::$configs[$key] = $value;
    }
}
