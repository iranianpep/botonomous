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
     *
     * @throws \Exception
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
        if (empty($this->getRequest('debug'))) {
            $logger = new LoggerUtility();
            $logger->logRaw((new FormattingUtility())->newLine());
            $logger->logChat(__METHOD__, $this->getRequest('text'));
        }

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
        $debug = (bool) $this->getRequest('debug');

        $data = [
             'text' => $response,
        ];

        $logChat = new LoggerUtility();

        if ($debug === true) {
            echo json_encode($data);
        } elseif ($responseType === 'slack') {
            $logChat->logChat(__METHOD__, $response);
            (new ApiClient())->chatPostMessage($data);
        } elseif ($responseType === 'json') {
            $logChat->logChat(__METHOD__, $response);
            // headers_sent is used to avoid issue in the test
            if (!headers_sent()) {
                header('Content-type:application/json;charset=utf-8');
            }
            echo json_encode($data);
        }

        return true;
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
        try {
            $result = $this->getPluginAction($message);

            if (empty($result['plugin']) || empty($result['action'])) {
                return $result['error'];
            }

            $pluginClass = $result['plugin'];
            $action = $result['action'];

            return $pluginClass->$action();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $message
     *
     * @throws \Exception
     *
     * @return array|mixed
     */
    public function getPluginAction($message)
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

        // check the plugin
        if (!isset($commandDetails['plugin'])) {
            throw new \Exception('Plugin is not set for this command');
        }

        // check the action
        if (!isset($commandDetails['action'])) {
            throw new \Exception('Action is not set for this command');
        }

        // create the class
        $pluginClassFile = $commandDetails['class'];
        $pluginClass = new $pluginClassFile($this);

        // check class is valid
        if (!$pluginClass instanceof AbstractPlugin) {
            throw new \Exception("Couldn't create class: '{$pluginClassFile}'");
        }

        // check action exists
        $action = $commandDetails['action'];
        if (!method_exists($pluginClass, $action)) {
            throw new \Exception("Action / function: '{$action}' does not exist in '{$pluginClassFile}'");
        }

        return [
            'plugin' => $pluginClass,
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
