<?php

namespace Slackbot;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use Slackbot\client\ApiClient;
use Slackbot\plugin\AbstractPlugin;

/**
 * Class Slackbot.
 */
class Slackbot extends AbstractBot
{
    private $commands;
    private $lastError;
    private $currentCommand;

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
        date_default_timezone_set($this->getConfig()->get('timezone'));
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
     * @return string
     */
    private function determineAction()
    {
        $getRequest = $this->getRequestUtility()->getGet();
        $action = '';
        if (isset($getRequest['action'])) {
            $action = strtolower($getRequest['action']);
        }

        return $action;
    }

    /**
     * @return MessageAction|void
     */
    private function handleMessageActions()
    {
        $post = $this->getRequestUtility()->getPost();

        // ignore if payload is not set
        if (!isset($post['payload'])) {
            return;
        }

        // $post['payload'] contains JSON
        $payload = json_decode($post['payload'], true);

        return (new MessageAction())->load($payload);
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        switch ($this->determineAction()) {
            case 'oauth':
                $this->getOauth()->doOauth();
                break;
            case 'message_actions':
                $this->handleMessageActions();
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
                 * 4. check the blacklist.
                 */
                if ($this->getConfig()->get('enabledAccessControl') === true) {
                    $blackList = new BlackList($this->getListener()->getRequest());
                    if ($blackList->isBlackListed() !== false) {
                        // found in blacklist
                        $this->send($this->getRequest('channel_name'), $this->getConfig()->get('blacklistedMessage'));
                        break;
                    }

                    $whitelist = new WhiteList($this->getListener()->getRequest());
                    if ($whitelist->isWhiteListed() !== true) {
                        // not found in whitelist
                        $this->send($this->getRequest('channel_name'), $this->getConfig()->get('whitelistedMessage'));
                        break;
                    }
                }

                /*
                 * 4. set the current command.
                 */
                $message = $this->getMessage();
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
                $this->send($this->getRequest('channel_name'), $this->respond($message));
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
    public function send($channel, $response, $attachments = null)
    {
        // @codeCoverageIgnoreStart
        if ($this->getListener()->isThisBot() !== false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        $responseType = $this->getConfig()->get('response');
        $debug = (bool) $this->getRequest('debug');

        if (empty($channel)) {
            $channel = $this->getConfig()->get('channel');
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
            $this->getLoggerUtility()->logChat(__METHOD__, $response);
            (new ApiClient())->chatPostMessage($data);
        } elseif ($responseType === 'slashCommand') {
            /** @noinspection PhpUndefinedClassInspection */
            $request = new Request(
                'POST',
                $this->getRequest('response_url'),
                ['Content-Type' => 'application/json'],
                json_encode([
                    'text'          => $response,
                    'response_type' => 'in_channel',
                ])
            );

            /* @noinspection PhpUndefinedClassInspection */
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
            $message = $this->getMessage();
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
     * Return message based on the listener
     * If listener is event and event text is empty, fall back to request text.
     *
     * @return mixed|string
     */
    public function getMessage()
    {
        $listener = $this->getListener();
        if ($listener instanceof EventListener && $listener->getEvent() instanceof Event) {
            $message = $listener->getEvent()->getText();

            if (!empty($message)) {
                return $message;
            }
        }

        return $listener->getRequest('text');
    }
}
