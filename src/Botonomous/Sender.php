<?php

namespace Botonomous;

use Botonomous\listener\EventListener;
use Botonomous\listener\SlashCommandListener;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;

class Sender extends AbstractSender
{
    const SLACK_RESPONSE_TYPE = 'slack';
    const SLASH_COMMAND_RESPONSE_TYPE = 'slashCommand';
    const JSON_RESPONSE_TYPE = 'json';

    private $slackbot;

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
     * @return AbstractBot
     */
    public function getSlackbot(): AbstractBot
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

        if (empty($channel)) {
            $channel = $this->getSlackbot()->getListener()->getChannelId();
        }

        $data = [
            'text'    => $text,
            'channel' => $channel,
        ];

        if ($attachments !== null) {
            $data['attachments'] = json_encode($attachments);
        }

        $this->getLoggerUtility()->logChat(__METHOD__, $text, $channel);

        $responseType = $this->getResponseType();
        if ($responseType === self::SLACK_RESPONSE_TYPE) {
            $this->respondToSlack($data);
        } elseif ($responseType === self::SLASH_COMMAND_RESPONSE_TYPE) {
            $this->respondToSlashCommand($text);
        } elseif ($responseType === self::JSON_RESPONSE_TYPE) {
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
     * Specify the response type
     * If response in config is set to empty, it will be considered based on listener.
     *
     * @return mixed|string
     */
    private function getResponseType()
    {
        if ($this->getSlackbot()->getRequest('debug') === true
            || $this->getSlackbot()->getRequest('debug') === 'true') {
            return self::JSON_RESPONSE_TYPE;
        }

        // response type in the config is empty, so choose it based on listener type
        return $this->getResponseByListenerType();
    }

    /**
     * @return string
     */
    private function getResponseByListenerType(): string
    {
        $listener = $this->getConfig()->get('listener');
        switch ($listener) {
            case SlashCommandListener::KEY:
                return self::SLASH_COMMAND_RESPONSE_TYPE;
            case EventListener::KEY:
                return self::SLACK_RESPONSE_TYPE;
            default:
                return self::SLASH_COMMAND_RESPONSE_TYPE;
        }
    }

    /**
     * Send confirmation.
     */
    public function sendConfirmation()
    {
        $userId = $this->getSlackbot()->getRequest('user_id');

        $user = '';
        if (!empty($userId)) {
            $user = $this->getMessageUtility()->linkToUser($userId).' ';
        }

        $confirmMessage = $this->getSlackbot()->getDictionary()->getValueByKey(
            'generic-messages',
            'confirmReceivedMessage',
            ['user' => $user]
        );

        if (!empty($confirmMessage)) {
            $this->send($confirmMessage);
        }
    }
}
