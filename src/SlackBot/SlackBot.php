<?php

namespace Slackbot;

/**
 * Class Slackbot
 * @package Slackbot
 */
class Slackbot
{
    const END_POINT = 'https://slack.com/api/chat.postMessage';
    const API_TOKEN = 'YOUR_API_TOKEN';
    const CHANNEL_NAME = '#general';
    const BOT_USERNAME = 'YOUR_BOT_USERNAME';
    const OUTGOING_WEBHOOK_TOKEN = 'YOUR_OUTGOING_WEBHOOK_TOKEN';
    const CHAT_LOGGING = true;
    const CHAT_LOGGING_FILE_NAME = 'chat_log.txt';
    const ICON_URL = 'YOUR_BOT_ICON_URL_48_BY_48';

    private $receivedData;

    /**
     * Slackbot constructor.
     */
    public function __construct()
    {
        $this->receivedData = $_POST;

        if ($this->verifyRequest() !== true) {
            //throw new \Exception('Request is not valid');
            echo 'Request is not coming from Slack';
            exit;
        }
    }

    /**
     * Listen to incoming requests from Slack
     */
    public function listen()
    {
        $this->logChat($this->receivedData['text'], __METHOD__);

        $this->send('Your message received');
        // Body goes here
    }

    /**
     * @param $message
     *
     * @return bool|mixed
     */
    public function send($message)
    {
        if ($this->isThisBot()) {
            return false;
        }

        $ch = curl_init(self::END_POINT);
        $data = http_build_query([
            'token' => self::API_TOKEN,
            'channel' => self::CHANNEL_NAME,
            'text' => $message,
            'username' => self::BOT_USERNAME,
            'as_user' => false,
            'icon_url' => self::ICON_URL
        ]);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->logChat($message, __METHOD__);

        return $result;
    }

    /**
     * @return bool
     */
    private function isThisBot()
    {
        if ((isset($this->receivedData['user_id']) && $this->receivedData['user_id'] == 'USLACKBOT')
        || (isset($this->receivedData['user_name']) && $this->receivedData['user_name'] == 'slackbot')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function verifyRequest()
    {
        if (isset($this->receivedData['token'])
            && $this->receivedData['token'] === self::OUTGOING_WEBHOOK_TOKEN
            && $this->isThisBot() == false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param        $function
     * @param string $message
     *
     * @return bool
     */
    private function logChat($function, $message = '')
    {
        if (self::CHAT_LOGGING !== true) {
            return false;
        }

        $currentTime = date('Y-m-d H:i:s');

        file_put_contents(
            self::CHAT_LOGGING_FILE_NAME,
            "{$currentTime}|{$function}|{$message}\r\n",
            FILE_APPEND
        );
    }
}

/**
 * Start the engine
 */
(new Slackbot())->listen();
