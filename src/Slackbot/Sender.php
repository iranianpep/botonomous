<?php

namespace Slackbot;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use Slackbot\client\ApiClient;

class Sender
{
    private $slackbot;

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

        $responseType = $this->getSlackbot()->getConfig()->get('response');
        $debug = (bool) $this->getSlackbot()->getRequest('debug');

        if (empty($channel)) {
            $channel = $this->getSlackbot()->getConfig()->get('channel');
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
            $this->getSlackbot()->getLoggerUtility()->logChat(__METHOD__, $response);
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
            $this->getSlackbot()->getLoggerUtility()->logChat(__METHOD__, $response);
            // headers_sent is used to avoid issue in the test
            if (!headers_sent()) {
                header('Content-type:application/json;charset=utf-8');
            }
            echo json_encode($data);
        }

        return true;
    }
}
