<?php

namespace Slackbot;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Slackbot\client\ApiClient;
use Slackbot\plugin\AbstractPlugin;
use Slackbot\utility\FormattingUtility;
use Slackbot\utility\LoggerUtility;
use Slackbot\utility\MessageUtility;
use Slackbot\utility\RequestUtility;

/**
 * Class Slackbot.
 */
class Slackbot
{
    private $commands;
    private $lastError;
    private $currentCommand;

    /**
     * Dependencies.
     */
    private $config;
    private $listener;
    private $messageUtility;
    private $commandContainer;
    private $formattingUtility;
    private $loggerUtility;
    private $oauth;
    private $requestUtility;

    /**
     * Slackbot constructor.
     *
     * @param Config|null $config
     *
     * @throws \Exception
     */
    public function __construct(Config $config = null)
    {
        if ($config !== null) {
            $this->setConfig($config);
        }

        // set timezone
        date_default_timezone_set($this->getConfig()->get('defaultTimeZone'));
    }

    /**
     * @param null $key
     *
     * @return mixed
     */
    public function getRequest($key = null)
    {
        return $this->getListener()->getRequest($key);
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        // Get action
        $getRequest = $this->getRequestUtility()->getGet();
        $action = '';
        if (isset($getRequest['action'])) {
            $action = strtolower($getRequest['action']);
        }

        switch ($action) {
            case 'oauth':
                $this->getOauth()->doOauth();
                break;
            default:
                /*
                 * 1. Start listening
                 */
                $this->getListener()->listen();

                /*
                 * 2. verify the request
                 */
                try {
                    $verificationResult = $this->verifyRequest();

                    if ($verificationResult['success'] !== true) {
                        throw new \Exception($verificationResult['message']);
                    }
                } catch (\Exception $e) {
                    throw $e;
                }

                /*
                 * 3. pre process the request
                 */
                $this->preProcessRequest();

                /*
                 * 4. set the current command.
                 */
                $message = $this->getRequest('text');
                $this->setCurrentCommand($this->getMessageUtility()->extractCommandName($message));

                /*
                 * 5. log the message
                 */
                if (empty($this->getRequest('debug'))) {
                    $this->getLoggerUtility()->logRaw($this->getFormattingUtility()->newLine());
                    $this->getLoggerUtility()->logChat(__METHOD__, $message);
                }

                /*
                 * 6. send confirmation message if is enabled.
                 */
                $this->sendConfirmation();

                /*
                 * 7. And send the response to the channel
                 */
                $channel = $this->getRequest('channel_name');
                $this->send($channel, $this->respond($message));
                break;
        }
    }

    /**
     * Send confirmation.
     */
    private function sendConfirmation()
    {
        $userId = $this->getRequest('user_id');

        $user = '';
        if (!empty($userId)) {
            $user = $this->getMessageUtility()->linkToUser($userId).' ';
        }

        $confirmMessage = $this->getConfig()->get('confirmReceivedMessage', ['user' => $user]);

        $channel = $this->getRequest('channel_name');
        if (!empty($confirmMessage)) {
            $this->send($channel, $confirmMessage);
        }
    }

    /**
     * Pre-process the request.
     */
    private function preProcessRequest()
    {
        $request = $this->getListener()->getRequest();

        // remove the trigger_word from beginning of the message
        if (!empty($request['trigger_word'])) {
            $request['text'] = $this->getMessageUtility()->removeTriggerWord(
                $request['text'],
                $request['trigger_word']
            );

            $this->getListener()->setRequest($request);
        }
    }

    /**
     * Final endpoint for the response.
     *
     * @param $channel
     * @param $response
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function send($channel, $response)
    {
        // @codeCoverageIgnoreStart
        if ($this->getListener()->isThisBot() !== false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        $responseType = $this->getConfig()->get('response');
        $debug = (bool) $this->getRequest('debug');

        if (empty($channel)) {
            $channel = $this->getConfig()->get('channelName');
        }

        $data = [
            'text'    => $response,
            'channel' => '#'.$channel,
        ];

        if ($debug === true) {
            echo json_encode($data);
        } elseif ($responseType === 'slashCommand') {
            $client = new Client();

            $args = [
                'text'          => $response,
                'response_type' => 'in_channel',
            ];

            $request = new Request(
                'POST',
                $this->getRequest('response_url'),
                ['Content-Type' => 'application/json'],
                json_encode($args)
            );

            $client->send($request);
        } elseif ($responseType === 'slack') {
            $this->getLoggerUtility()->logChat(__METHOD__, $response);
            (new ApiClient())->chatPostMessage($data);
        } elseif ($responseType === 'slashCommand') {
            $args = [
                'text'          => $response,
                'response_type' => 'in_channel',
            ];

            /** @noinspection PhpUndefinedClassInspection */
            $request = new Request(
                'POST',
                $this->getRequest('response_url'),
                ['Content-Type' => 'application/json'],
                json_encode($args)
            );

            (new Client())->send($request);
        } elseif ($responseType === 'json') {
            $this->getLoggerUtility()->logChat(__METHOD__, $response);
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
     * @throws \Exception
     *
     * @return bool|Command
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
        $command = $this->getMessageUtility()->extractCommandName($message);

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

        $commandObject = $this->getCommandContainer()->getAsObject($command);

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
     * @throws \Exception
     *
     * @return array
     */
    private function verifyRequest()
    {
        $originCheck = $this->getListener()->verifyOrigin();

        if (!isset($originCheck['success'])) {
            throw new \Exception('Success must be provided in verifyOrigin response');
        }

        if ($originCheck['success'] !== true) {
            return [
                'success' => false,
                'message' => $originCheck['message'],
            ];
        }

        if ($this->getListener()->isThisBot() !== false) {
            return [
                'success' => false,
                'message' => 'Request comes from the bot',
            ];
        }

        return [
            'success' => true,
            'message' => 'Yay!',
        ];
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
            $this->setCommands($this->getCommandContainer()->getAllAsObject());
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

    /**
     * @return string
     */
    public function getCurrentCommand()
    {
        return $this->currentCommand;
    }

    /**
     * @param string $currentCommand
     */
    public function setCurrentCommand($currentCommand)
    {
        $this->currentCommand = $currentCommand;
    }

    /**
     * @return BaseListener
     */
    public function getListener()
    {
        if (!isset($this->listener)) {
            $rootNamespace = $this->getConfig()->get('rootNamespace');
            $listenerClass = $rootNamespace.'\\'.ucwords($this->getConfig()->get('listenerType')).'Listener';
            $this->setListener(new $listenerClass());
        }

        return $this->listener;
    }

    /**
     * @param BaseListener $listener
     */
    public function setListener(BaseListener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return MessageUtility
     */
    public function getMessageUtility()
    {
        if (!isset($this->messageUtility)) {
            $this->setMessageUtility(new MessageUtility());
        }

        return $this->messageUtility;
    }

    /**
     * @param MessageUtility $messageUtility
     */
    public function setMessageUtility(MessageUtility $messageUtility)
    {
        $this->messageUtility = $messageUtility;
    }

    /**
     * @return CommandContainer
     */
    public function getCommandContainer()
    {
        if (!isset($this->commandContainer)) {
            $this->setCommandContainer(new CommandContainer());
        }

        return $this->commandContainer;
    }

    /**
     * @param CommandContainer $commandContainer
     */
    public function setCommandContainer(CommandContainer $commandContainer)
    {
        $this->commandContainer = $commandContainer;
    }

    /**
     * @return FormattingUtility
     */
    public function getFormattingUtility()
    {
        if (!isset($this->formattingUtility)) {
            $this->setFormattingUtility(new FormattingUtility());
        }

        return $this->formattingUtility;
    }

    /**
     * @param FormattingUtility $formattingUtility
     */
    public function setFormattingUtility(FormattingUtility $formattingUtility)
    {
        $this->formattingUtility = $formattingUtility;
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
     * @return OAuth
     */
    public function getOauth()
    {
        if (!isset($this->oauth)) {
            $this->setOauth(new OAuth());
        }

        return $this->oauth;
    }

    /**
     * @param OAuth $oauth
     */
    public function setOauth(OAuth $oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * @return RequestUtility
     */
    public function getRequestUtility()
    {
        if (!isset($this->requestUtility)) {
            $this->setRequestUtility((new RequestUtility()));
        }

        return $this->requestUtility;
    }

    /**
     * @param RequestUtility $requestUtility
     */
    public function setRequestUtility(RequestUtility $requestUtility)
    {
        $this->requestUtility = $requestUtility;
    }
}
