<?php

namespace Slackbot;

/**
 * Class Slackbot
 * @package Slackbot
 */
class Slackbot
{
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

        $config = $this->getConfig();

        $ch = curl_init($config->get('endPoint'));
        $data = http_build_query([
            'token' => $config->get('apiToken'),
            'channel' => $config->get('channelName'),
            'text' => $message,
            'username' => $config->get('botUsername'),
            'as_user' => false,
            'icon_url' => $config->get('iconURL')
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
            && $this->receivedData['token'] === $this->getConfig()->get('outgoingWebhookToken')
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
        $config = $this->getConfig();
        
        if ($config->get('chatLogging') !== true) {
            return false;
        }

        $currentTime = date('Y-m-d H:i:s');

        file_put_contents(
            $config->get('chatLoggingFileName'),
            "{$currentTime}|{$function}|{$message}\r\n",
            FILE_APPEND
        );
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return (new Config());
    }
}

/**
 * Start the engine
 */
(new Slackbot())->listen();
