<?php

namespace Slackbot;

use Slackbot\utility\RequestUtility;

abstract class BaseListener
{
    private $config;
    private $request;
    private $requestUtility;

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

        /* @noinspection PhpInconsistentReturnPointsInspection */
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

    /**
     * @return RequestUtility
     */
    public function getRequestUtility()
    {
        if (!isset($this->requestUtility)) {
            $this->setRequestUtility((new RequestUtility()));
        }

        return $this->requestUtility;
    }

    /**
     * @param RequestUtility $requestUtility
     */
    public function setRequestUtility(RequestUtility $requestUtility)
    {
        $this->requestUtility = $requestUtility;
    }

    /**
     * respondOK.
     */
    protected function respondOK()
    {
        if ($this->getConfig()->get('respondOk') !== true) {
            return false;
        }

        // check if fastcgi_finish_request is callable
        if (is_callable('fastcgi_finish_request')) {
            /*
             * http://stackoverflow.com/a/38918192
             * This works in Nginx but the next approach not
             */
            session_write_close();
            fastcgi_finish_request();

            return;
        }

        ignore_user_abort(true);

        ob_start();
        header($this->getRequestUtility()->getServerProtocol().' 200 OK');
        // Disable compression (in case content length is compressed).
        header('Content-Encoding: none');
        header('Content-Length: '.ob_get_length());

        // Close the connection.
        header('Connection: close');

        ob_end_flush();
        ob_flush();
        flush();
    }
}
