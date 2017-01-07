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
    private $commands;
    private $lastError;

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

    /** @noinspection PhpInconsistentReturnPointsInspection
     * @param null $key
     *
     * @return
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
            $confirmMessage = $this->getConfig()->get('confirmReceivedMessage');

            if (!empty($confirmMessage)) {
                $this->send($confirmMessage);
            }

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
        // @codeCoverageIgnoreStart
        if ($this->isThisBot()) {
            return false;
        }
        // @codeCoverageIgnoreEnd

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
            $command = $this->getCommandByMessage($message);

            if (!$command instanceof Command) {
                // something went wrong, error will tell us!
                return $this->getLastError();
            }

            // create the class
            $pluginClassFile = $command->getClass();
            $pluginClass = new $pluginClassFile($this);

            // check class is valid
            if (!$pluginClass instanceof AbstractPlugin) {
                throw new \Exception("Couldn't create class: '{$pluginClassFile}'");
            }

            // check action exists
            $action = $command->getAction();
            if (!method_exists($pluginClass, $action)) {
                throw new \Exception("Action / function: '{$action}' does not exist in '{$pluginClassFile}'");
            }

            return $pluginClass->$action();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param null $message
     *
     * @return bool|Command
     * @throws \Exception
     */
    public function getCommandByMessage($message = null)
    {
        // If message is not set, get it from the current request
        if ($message === null) {
            $message = $this->getRequest('text');
        }

        if (empty($message)) {
            $this->setLastError('Message is empty');
            return false;
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
                $this->setLastError($config->get('noCommandMessage'));
                return false;
            }
        }

        $commandObject = (new CommandContainer())->get($command);

        // check command details
        if (empty($commandObject)) {
            $this->setLastError($config->get('unknownCommandMessage', ['command' => $command]));
            return false;
        }

        if (!$commandObject instanceof Command) {
            throw new \Exception('Command is not an object');
        }

        // check the plugin for the command
        if (empty($commandObject->getPlugin())) {
            throw new \Exception('Plugin is not set for this command');
        }

        return $commandObject;
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

    /**
     * @return array
     */
    public function getCommands()
    {
        if (!isset($this->commands)) {
            $this->setCommands((new CommandContainer())->getAll());
        }

        return $this->commands;
    }

    /**
     * @param array $commands
     */
    public function setCommands(array $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param string $lastError
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;
    }
}
