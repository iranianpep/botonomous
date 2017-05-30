<?php

namespace Botonomous\listener;

use Botonomous\Config;
use Botonomous\utility\RequestUtility;

abstract class AbstractBaseListener
{
    private $config;
    private $request;
    private $requestUtility;

    /**
     * listen.
     *
     * @return mixed
     */
    public function listen()
    {
        // This is needed otherwise timeout error is displayed
        $this->respondOK();

        $request = $this->extractRequest();

        if (empty($request)) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        $this->setRequest($request);

        if ($this->isThisBot() !== false) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return $request;
    }

    /**
     * @return mixed
     */
    abstract public function extractRequest();

    /**
     * @return string
     */
    abstract public function getChannelId();

    /**
     * @param null|string $key
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
     * @return bool
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
        // check if fastcgi_finish_request is callable
        if (is_callable('fastcgi_finish_request')) {
            /*
             * http://stackoverflow.com/a/38918192
             * This works in Nginx but the next approach not
             */
            session_write_close();
            fastcgi_finish_request();

            /* @noinspection PhpInconsistentReturnPointsInspection */
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
        // only if an output buffer is active do ob_flush
        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();

        /* @noinspection PhpInconsistentReturnPointsInspection */
    }

    /**
     * @throws \Exception
     *
     * @return array<string,boolean|string>
     */
    public function verifyRequest()
    {
        $originCheck = $this->verifyOrigin();

        if (!isset($originCheck['success'])) {
            throw new \Exception('Success must be provided in verifyOrigin response');
        }

        if ($originCheck['success'] !== true) {
            return [
                'success' => false,
                'message' => $originCheck['message'],
            ];
        }

        if ($this->isThisBot() !== false) {
            return [
                'success' => false,
                'message' => 'Request comes from the bot',
            ];
        }

        return [
            'success' => true,
            'message' => 'Yay!',
        ];
    }

    /**
     * @return string|null
     */
    public function determineAction()
    {
        $utility = $this->getRequestUtility();
        $getRequest = $utility->getGet();

        if (!empty($getRequest['action'])) {
            return strtolower($getRequest['action']);
        }

        $request = $utility->getPostedBody();

        if (isset($request['type']) && $request['type'] === 'url_verification') {
            return 'url_verification';
        }
    }
}
