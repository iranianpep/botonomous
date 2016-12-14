<?php

namespace Slackbot\utility;

use Slackbot\Config;

abstract class AbstractUtility
{
    private $config;
    
    public function __construct(Config $config = null)
    {
        if ($config !== null) {
            $this->setConfig($config);
        }
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = new Config();
        }
        
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
