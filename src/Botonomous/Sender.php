<?php

namespace Botonomous;

use Botonomous\client\ApiClient;
use Botonomous\utility\LoggerUtility;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;

class Sender
{
    private $slackbot;
    private $loggerUtility;
    private $config;
    private $apiClient;
    private $client;

    /**
     * Sender constructor.
     *
     * @param AbstractBot $slackbot
     */
    public function __construct(AbstractBot $slackbot)
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
     * @param AbstractBot $slackbot
     */
    public function setSlackbot(AbstractBot $slackbot)
    {
        $this->slackbot = $slackbot;
    }

    /**
     * Final endpoint for the response.
     *
     * @param $channel
     * @param $text
     * @param $attachments
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function send($text, $channel = null, $attachments = null)
    {
        // @codeCoverageIgnoreStart
        if ($this->getSlackbot()->getListener()->isThisBot() !== false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        $responseType = $this->getResponseType();

        if (empty($channel)) {
            $channel = $this->getSlackbot()->getListener()->getChannelId();

            if (empty($channel)) {
                $channel = $this->getConfig()->get('channel');
            }
        }

        $data = [
            'text'    => $text,
            'channel' => $channel,
        ];

        if ($attachments !== null) {
            $data['attachments'] = json_encode($attachments);
        }

        $this->getLoggerUtility()->logChat(__METHOD__, $text, $channel);

        if ($responseType === 'slack') {
            $this->respondToSlack($data);
        } elseif ($responseType === 'slashCommand') {
            $this->respondToSlashCommand($text);
        } elseif ($responseType === 'json') {
            $this->respondAsJSON($data);
        }

        return true;
    }

    /**
     * @param $response
     */
    private function respondToSlashCommand($response)
    {
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
        $this->getClient()->send($request);
    }

    /**
     * @param $data
     */
    private function respondToSlack($data)
    {
        $this->getApiClient()->chatPostMessage($data);
    }

    /**
     * @param $data
     */
    private function respondAsJSON($data)
    {
        // headers_sent is used to avoid issue in the test
        if (!headers_sent()) {
            header('Content-type:application/json;charset=utf-8');
        }

        echo json_encode($data);
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
        if (!isset($this->config)) {
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

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        if (!isset($this->apiClient)) {
            $this->setApiClient(new ApiClient());
        }

        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!isset($this->client)) {
            $this->setClient(new Client());
        }

        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Specify the response type
     * If response in config is set to empty, it will be considered based on listener.
     *
     * @return mixed|string
     */
    private function getResponseType()
    {
        if ($this->getSlackbot()->getRequest('debug') === true
            || $this->getSlackbot()->getRequest('debug') === 'true') {
            return 'json';
        }

        $responseType = $this->getConfig()->get('response');
        if (!empty($responseType)) {
            // Maybe later add a check for response type validation
            return $responseType;
        }

        // response type in the config is empty, so choose it based on listener type
        return $this->getResponseByListenerType();
    }

    /**
     * @return string|null
     */
    private function getResponseByListenerType()
    {
        $listener = $this->getConfig()->get('listener');
        switch ($listener) {
            case 'slashCommand':
                return 'slashCommand';
            case 'event':
                return 'slack';
        }
    }
}
