<?php

namespace Slackbot;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use Slackbot\client\ApiClient;
use Slackbot\utility\LoggerUtility;

class Sender
{
    private $slackbot;
    private $loggerUtility;
    private $config;

    /**
     * Sender constructor.
     *
     * @param $slackbot
     */
    public function __construct($slackbot)
    {
        $this->setSlackbot($slackbot);
    }

    /**
     * @return Slackbot
     */
    public function getSlackbot()
    {
        return $this->slackbot;
    }

    /**
     * @param Slackbot $slackbot
     */
    public function setSlackbot(Slackbot $slackbot)
    {
        $this->slackbot = $slackbot;
    }

    /**
     * Final endpoint for the response.
     *
     * @param $channel
     * @param $response
     * @param $attachments
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function send($channel, $response, $attachments = null)
    {
        // @codeCoverageIgnoreStart
        if ($this->getSlackbot()->getListener()->isThisBot() !== false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        $responseType = $this->getConfig()->get('response');
        $debug = (bool) $this->getSlackbot()->getRequest('debug');

        if (empty($channel)) {
            $channel = $this->getConfig()->get('channel');
        }

        $data = [
            'text'    => $response,
            'channel' => $channel,
        ];

        if ($attachments !== null) {
            $data['attachments'] = json_encode($attachments);
        }

        if ($debug === true) {
            echo json_encode($data);
        } elseif ($responseType === 'slack') {
            $this->getLoggerUtility()->logChat(__METHOD__, $response);
            (new ApiClient())->chatPostMessage($data);
        } elseif ($responseType === 'slashCommand') {
            /** @noinspection PhpUndefinedClassInspection */
            $request = new Request(
                'POST',
                $this->getSlackbot()->getRequest('response_url'),
                ['Content-Type' => 'application/json'],
                json_encode([
                    'text'          => $response,
                    'response_type' => 'in_channel',
                ])
            );

            /* @noinspection PhpUndefinedClassInspection */
            (new Client())->send($request);
        } elseif ($responseType === 'json') {
            $this->getLoggerUtility()->logChat(__METHOD__, $response);
            // headers_sent is used to avoid issue in the test
            if (!headers_sent()) {
                header('Content-type:application/json;charset=utf-8');
            }
            echo json_encode($data);
        }

        return true;
    }

    /**
     * @return LoggerUtility
     */
    public function getLoggerUtility()
    {
        if (!isset($this->loggerUtility)) {
            $this->setLoggerUtility(new LoggerUtility());
        }

        return $this->loggerUtility;
    }

    /**
     * @param LoggerUtility $loggerUtility
     */
    public function setLoggerUtility(LoggerUtility $loggerUtility)
    {
        $this->loggerUtility = $loggerUtility;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = (new Config());
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
