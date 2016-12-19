<?php

namespace Slackbot;

use Slackbot\client\ApiClient;
use Slackbot\plugin\AbstractPlugin;
use Slackbot\utility\FormattingUtility;
use Slackbot\utility\LoggerUtility;
use Slackbot\utility\MessageUtility;

/**
 * Class Slackbot.
 */
class Slackbot
{
    private $request;
    private $config;

    /**
     * Slackbot constructor.
     *
     * @param $request
     * @param Config|null $config
     */
    public function __construct($request, Config $config = null)
    {
        // set timezone
        date_default_timezone_set($this->getConfig()->get('defaultTimeZone'));

        if ($config !== null) {
            $this->setConfig($config);
        }

        try {
            $this->setRequest($request);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $request
     *
     * @throws \Exception
     */
    public function setRequest($request)
    {
        $this->request = $request;

        if ($this->verifyRequest() !== true) {
            throw new \Exception('Request is not coming from Slack');
        }
    }

    /**
     * @param null $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getRequest($key = null)
    {
        if ($key === null) {
            // return the entire request since key is null
            return $this->request;
        }

        if (array_key_exists($key, $this->request)) {
            return $this->request[$key];
        }
    }

    /**
     * Listen to incoming requests from Slack.
     */
    public function listenToSlack()
    {
        $logger = new LoggerUtility();
        $logger->logRaw((new FormattingUtility())->newLine());
        $logger->logChat(__METHOD__, $this->getRequest('text'));

        try {
            $response = $this->respond($this->getRequest('text'));
            $this->send($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Final endpoint for the response.
     *
     * @param $response
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function send($response)
    {
        if ($this->isThisBot()) {
            return false;
        }

        $responseType = $this->getConfig()->get('response');

        $data = [
             'text' => $response,
        ];

        $logChat = new LoggerUtility();
        $logChat->logChat(__METHOD__, $response);

        if ($responseType === 'slack') {
            (new ApiClient())->chatPostMessage($data);
        } elseif ($responseType === 'json') {
            // headers_sent is used to avoid issue in the test
            if (!headers_sent()) {
                header('Content-type:application/json;charset=utf-8');
            }
            echo json_encode($data);
        }
    }

    /**
     * @param null $message
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function respond($message = null)
    {
        $result = $this->getModuleAction($message);

        if (empty($result['module']) || empty($result['action'])) {
            return $result['error'];
        }

        $moduleClass = $result['module'];
        $action = $result['action'];

        return $moduleClass->$action();
    }

    /**
     * @param $message
     *
     * @throws \Exception
     *
     * @return array|mixed
     */
    public function getModuleAction($message)
    {
        // If message is not set, get it from the current request
        if ($message === null) {
            $message = $this->getRequest('text');
        }

        /**
         * Process the message.
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
            return ['error' => $config->get('unknownCommandMessage', ['command' => $command])];
        }

        // check the module
        if (!isset($commandDetails['module'])) {
            throw new \Exception('Module is not set for this command');
        }

        // check the action
        if (!isset($commandDetails['action'])) {
            throw new \Exception('Action is not set for this command');
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
            'action' => $action,
        ];
    }

    /**
     * @return bool
     */
    private function isThisBot()
    {
        $userId = $this->getRequest('user_id');
        $username = $this->getRequest('user_name');

        return (isset($userId) && $userId == 'USLACKBOT')
        || (isset($username) && $username == 'slackbot') ? true : false;
    }

    /**
     * @return bool
     */
    private function verifyRequest()
    {
        $token = $this->getRequest('token');

        return isset($token) && $token === $this->getConfig()->get('outgoingWebhookToken')
        && $this->isThisBot() === false ? true : false;
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
