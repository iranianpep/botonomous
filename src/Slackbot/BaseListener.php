<?php

namespace Slackbot;

abstract class BaseListener
{
    private $config;
    private $request;

    abstract public function listen();

    abstract public function extractRequest();

    /**
     * @param null $key
     *
     * @return mixed
     */
    public function getRequest($key = null)
    {
        if (!isset($this->request)) {
            // each listener has its own way of extracting the request
            $this->setRequest($this->extractRequest());
        }

        if ($key === null) {
            // return the entire request since key is null
            return $this->request;
        }

        if (is_array($this->request) && array_key_exists($key, $this->request)) {
            return $this->request[$key];
        }
    }

    /**
     * @param array $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!isset($this->config)) {
            $this->setConfig(new Config());
        }

        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Verify the request comes from Slack
     * Each listener must have have this and has got its own way to check the request.
     *
     * @throws \Exception
     *
     * @return array
     */
    abstract public function verifyOrigin();

    /**
     * Check if the request belongs to the bot itself.
     *
     * @throws \Exception
     *
     * @return array
     */
    abstract public function isThisBot();
}
