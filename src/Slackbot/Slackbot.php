<?php

namespace Slackbot;

/**
 * Class Slackbot
 * @package Slackbot
 */
class Slackbot
{
    private $request;

    /**
     * Slackbot constructor.
     */
    public function __construct()
    {
        $this->setRequest($_POST);

        if ($this->verifyRequest() !== true) {
            //throw new \Exception('Request is not valid');
            echo 'Request is not coming from Slack';
            exit;
        }
    }

    /**
     * @param $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param null $key
     * @return mixed
     * @throws \Exception
     */
    public function getRequest($key = null)
    {
        if ($key === null) {
            // return the entire request since key is null
            return $this->request;
        } else {
            if (!array_key_exists($key, $this->request)) {
                throw new \Exception("Key: '{$key}' does not exist in the request");
            }

            return $this->request[$key];
        }
    }

    /**
     * Listen to incoming requests from Slack
     */
    public function listenToSlack()
    {
        $this->logChat($this->getRequest('text'), __METHOD__);

        $this->sendToSlack('Your message is received');
    }

    public function respond($message)
    {
        // TODO process the message here and return the response
    }

    /**
     * @param $message
     *
     * @return bool|mixed
     */
    public function sendToSlack($message)
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
        $userId = $this->getRequest('user_id');
        $username = $this->getRequest('user_name');

        if ((isset($userId) && $userId == 'USLACKBOT')
            || (isset($username) && $username == 'slackbot')) {
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
        $token = $this->getRequest('token');
        if (isset($token)
            && $token === $this->getConfig()->get('outgoingWebhookToken')
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
