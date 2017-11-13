<?php

namespace Botonomous;

/**
 * Class Command.
 */
class Command extends AbstractBaseSlack
{
    const DEFAULT_ACTION = 'index';
    const PLUGIN_DIR = 'plugin';

    private $key;
    private $plugin;
    private $description;
    private $action;
    private $class;
    private $keywords;

    /**
     * Command constructor.
     *
     * @param $key
     */
    public function __construct($key)
    {
        $this->setKey($key);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getPlugin(): string
    {
        return $this->plugin;
    }

    /**
     * @param string $plugin
     */
    public function setPlugin(string $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        if (empty($this->action)) {
            $this->setAction(self::DEFAULT_ACTION);
        }

        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        if (empty($this->class)) {
            $class = __NAMESPACE__.'\\'.self::PLUGIN_DIR.'\\'.strtolower($this->getPlugin()).'\\'.$this->getPlugin();
            $this->setClass($class);
        }

        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }
}
