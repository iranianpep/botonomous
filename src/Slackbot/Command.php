<?php

namespace Slackbot;

class Command
{
    const DEFAULT_ACTION = 'index';
    const PLUGIN_DIR = 'plugin';

    private $key;
    private $plugin;
    private $description;
    private $action;
    private $class;

    public function __construct($key)
    {
        $this->setKey($key);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @param string $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        if (empty($this->action)) {
            $this->setAction(self::DEFAULT_ACTION);
        }

        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getClass()
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
    public function setClass($class)
    {
        $this->class = $class;
    }
}
