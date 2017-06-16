<?php

namespace Botonomous\client;

use Botonomous\Config;
use Botonomous\utility\ArrayUtility;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;

/**
 * Class AbstractClient.
 */
abstract class AbstractClient
{
    private $client;
    private $config;
    private $arrayUtility;

    abstract public function apiCall($method, array $arguments = []);

    /** @noinspection PhpUndefinedClassInspection
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /** @noinspection PhpUndefinedClassInspection
     * @return Client|null
     */
    public function getClient()
    {
        if (!isset($this->client)) {
            /* @noinspection PhpUndefinedClassInspection */
            $this->setClient(new Client());
        }

        return $this->client;
    }

    /**
     * @return ArrayUtility
     */
    public function getArrayUtility()
    {
        if (!isset($this->arrayUtility)) {
            $this->setArrayUtility(new ArrayUtility());
        }

        return $this->arrayUtility;
    }

    /**
     * @param ArrayUtility $arrayUtility
     */
    public function setArrayUtility(ArrayUtility $arrayUtility)
    {
        $this->arrayUtility = $arrayUtility;
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
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
