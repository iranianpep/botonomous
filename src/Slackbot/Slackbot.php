<?php

namespace Slackbot;

use Slackbot\plugin\AbstractPlugin;
use Slackbot\utility\MessageUtility;

/**
 * Class Slackbot
 * @package Slackbot
 */
class Slackbot
{
    private $request;

    /**
     * Slackbot constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->setRequest($request);
    }

    /**
     * @param $request
     */
    public function setRequest($request)
    {
        $this->request = $request;

        if ($this->verifyRequest() !== true) {
            //throw new \Exception('Request is not valid');
            echo 'Request is not coming from Slack';
            exit;
        }
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
            if (array_key_exists($key, $this->request)) {
                return $this->request[$key];
            }
        }

        return null;
    }

    /**
     * Listen to incoming requests from Slack
     */
    public function listenToSlack()
    {
        $this->logChat($this->getRequest('text'), __METHOD__);

        try {
            $response = $this->respond($this->getRequest('text'));
        } catch (\Exception $e) {
            // TODO this can be re-thrown and be handled in public/index.php
            echo $e->getMessage();
            exit;
        }

        $this->send($response);
    }

    /**
     * Final endpoint for the response
     *
     * @param $response
     * @return bool
     * @throws \Exception
     */
    public function send($response)
    {
        if ($this->isThisBot()) {
            return false;
        }

        $responseType = $this->getConfig()->get('response');

        $config = $this->getConfig();

        $data = [
             'token' => $config->get('apiToken'),
             'channel' => $config->get('channelName'),
             'text' => $response,
             'username' => $config->get('botUsername'),
             'as_user' => false,
             'icon_url' => $config->get('iconURL')
        ];

        $this->logChat($response, __METHOD__);

        if ($responseType === 'slack') {
            $result = $this->sendToSlack($data);

            // log the response from Slack
            $this->logChat($result, "{$responseType} response");
        } elseif ($responseType === 'json') {
            header('Content-type:application/json;charset=utf-8');
            echo json_encode($data);
        }

        exit;
    }

    /**
     * @param null $message
     * @return mixed
     * @throws \Exception
     */
    public function respond($message = null)
    {
        $result = $this->getModuleAction($message);

        if (!empty($result['module']) && !empty($result['action'])) {
            $moduleClass = $result['module'];
            $action = $result['action'];

            return $moduleClass->$action();
        } else {
            return $result['error'];
        }
    }

    /**
     * @param $message
     * @return array|mixed
     * @throws \Exception
     */
    public function getModuleAction($message) {
        // If message is not set, get it from the current request
        if ($message === null) {
            $message = $this->getRequest('text');
        }

        /**
         * Process the message
         */
        $command = (new MessageUtility())->extractCommandName($message);

        $config = $this->getConfig();

        // check command name
        if (empty($command)) {
            // get the default command if no command is find in the message
            $command = $config->get('defaultCommand');

            if (empty($command)) {
                return ['error' => $config->get('noCommandMessage')];
            }
        }

        $commandDetails = (new Command())->get($command);

        // check command details
        if (empty($commandDetails)) {
            //return "Sorry. I do not know anything about your command: '/{$command}'. I List the available commands using /help";
            return ['error' => $config->get('unknownCommandMessage', ['command' => $command])];
        }

        // check the module
        if (!isset($commandDetails['module'])) {
            throw new \Exception("Module is not set for this command");
        }

        // check the action
        if (!isset($commandDetails['action'])) {
            throw new \Exception("Action is not set for this command");
        }

        // create the class
        $moduleClassFile = $commandDetails['class'];
        $moduleClass = new $moduleClassFile($this);

        // check class is valid
        if (!$moduleClass instanceof AbstractPlugin) {
            throw new \Exception("Couldn't create class: '{$moduleClassFile}'");
        }

        // check action exists
        $action = $commandDetails['action'];
        if (!method_exists($moduleClass, $action)) {
            throw new \Exception("Action / function: '{$action}' does not exist in '{$moduleClassFile}'");
        }

        return [
            'module' => $moduleClass,
            'action' => $action
        ];
    }

    /**
     * @param $data
     *
     * @return bool|mixed
     */
    public function sendToSlack(array $data)
    {
        // This is already used in send(), but since this method is public check it againg
        if ($this->isThisBot()) {
            return false;
        }

        $config = $this->getConfig();

        $ch = curl_init($config->get('endPoint'));
        $data = http_build_query($data);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

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
        if (isset($token) && $token === $this->getConfig()->get('outgoingWebhookToken') && $this->isThisBot() == false) {
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
