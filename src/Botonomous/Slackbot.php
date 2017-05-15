<?php

namespace Botonomous;

use Botonomous\listener\EventListener;
use Botonomous\plugin\AbstractPlugin;

/**
 * Class Botonomous.
 */
class Slackbot extends AbstractBot
{
    private $commands;
    private $lastError;
    private $currentCommand;

    /**
     * Botonomous constructor.
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
     * @param null|string $key
     *
     * @return mixed
     */
    public function getRequest($key = null)
    {
        return $this->getListener()->getRequest($key);
    }

    /**
     * @return string|null
     */
    private function determineAction()
    {
        $utility = $this->getRequestUtility();
        $getRequest = $utility->getGet();

        if (!empty($getRequest['action'])) {
            return strtolower($getRequest['action']);
        }

        $request = $utility->getPostedBody();

        if (isset($request['type']) && $request['type'] === 'url_verification') {
            return 'url_verification';
        }
    }

    /**
     * @return null|AbstractBaseSlack
     */
    private function handleMessageActions()
    {
        $post = $this->getRequestUtility()->getPost();

        // ignore if payload is not set
        if (!isset($post['payload'])) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        // posted payload is in JSON
        $payload = json_decode($post['payload'], true);

        return (new MessageAction())->load($payload);
    }

    /**
     * @throws \Exception
     */
    private function handleSendResponse()
    {
        // 1. Start listening
        $this->getListener()->listen();

        // 2. verify the request
        try {
            $verificationResult = $this->verifyRequest();

            if ($verificationResult['success'] !== true) {
                throw new \Exception($verificationResult['message']);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        // 3. pre process the request
        $this->preProcessRequest();

        // 4. check access control
        if ($this->checkAccessControl() !== true) {
            return;
        }

        // 5. set the current command
        $message = $this->getMessage();
        $this->setCurrentCommand($this->getMessageUtility()->extractCommandName($message));

        // 6. log the message
        if (empty($this->getRequest('debug'))) {
            $this->getLoggerUtility()->logRaw($this->getFormattingUtility()->newLine());
            $this->getLoggerUtility()->logChat(__METHOD__, $message);
        }

        $this->getLoggerUtility()->logRaw($this->getRequestUtility()->getContent());

        // 7. send confirmation message if is enabled
        $this->getSender()->sendConfirmation();

        // 8. And send the response to the channel, only if the response is not empty
        $response = $this->respond($message);

        if (!empty($response)) {
            $this->getSender()->send($response);
        }
    }

    /**
     * @return bool
     */
    private function checkAccessControl()
    {
        // if accessControlEnabled is not set true ignore the check and return true
        if ($this->getConfig()->get('accessControlEnabled') !== true) {
            return true;
        }

        if ($this->getBlackList()->isBlackListed() !== false) {
            // found in blacklist
            $this->getSender()->send($this->getConfig()->get('blacklistedMessage'));

            return false;
        }

        if ($this->getWhiteList()->isWhiteListed() !== true) {
            // not found in whitelist
            $this->getSender()->send($this->getConfig()->get('whitelistedMessage'));

            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        switch ($this->determineAction()) {
            case 'oauth':
                return $this->handleOAuth();
            case 'message_actions':
                return $this->handleMessageActions();
            case 'url_verification':
                return $this->handleUrlVerification();
            default:
                return $this->handleSendResponse();
        }
    }

    /**
     * handle OAuth.
     */
    private function handleOAuth()
    {
        return $this->getOauth()->doOauth();
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    private function handleUrlVerification()
    {
        $request = $this->getRequestUtility()->getPostedBody();

        if (empty($request['challenge'])) {
            throw new \Exception('Challenge is missing for URL verification');
        }

        echo $request['challenge'];
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
     * @return array<string,boolean|string>
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

    /**
     * Determine if bot user id is mentioned in the message.
     *
     * @return bool
     */
    public function youTalkingToMe()
    {
        $message = $this->getMessage();

        if (empty($message)) {
            return false;
        }

        if ($this->getMessageUtility()->isBotMentioned($message) === true) {
            return true;
        }

        $listener = $this->getListener();
        // check direct messages
        return $listener instanceof EventListener && $listener->getEvent()->isDirectMessage() === true;
    }
}
