<?php

namespace Botonomous;

use Botonomous\utility\MessageUtility;

/**
 * Class CommandExtractor.
 */
class CommandExtractor
{
    private $config;
    private $error;
    private $messageUtility;
    private $dictionary;
    private $commandContainer;

    /**
     * @param null $message
     *
     * @return Command|void
     */
    public function getCommandByMessage($message)
    {
        if (empty($message)) {
            $this->setError('Message is empty');

            return;
        }

        /*
         * Process the message.
         */
        return $this->getCommandObjectByMessage($message);
    }

    /**
     * @param $message
     *
     * @return Command|void
     */
    private function getCommandObjectByMessage($message)
    {
        $command = $this->getMessageUtility()->extractCommandName($message);

        // check command name
        if (empty($command)) {
            // get the default command if no command is find in the message
            $command = $this->getConfig()->get('defaultCommand');

            if (empty($command)) {
                $this->setError($this->getDictionary()->get('generic-messages')['noCommandMessage']);

                return;
            }
        }

        return $this->getCommandObjectByCommand($command);
    }

    /**
     * @param $command
     *
     * @return Command|void
     */
    private function getCommandObjectByCommand($command)
    {
        $commandObject = $this->getCommandContainer()->getAsObject($command);

        if ($this->validateCommandObject($commandObject) !== true) {
            return;
        }

        return $commandObject;
    }

    /**
     * Validate the command object.
     *
     * @param Command|null $commandObject
     *
     * @return bool
     */
    private function validateCommandObject($commandObject)
    {
        // check command details
        if (empty($commandObject)) {
            $this->setError(
                $this->getDictionary()->getValueByKey('generic-messages', 'unknownCommandMessage')
            );

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
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
     * @return Dictionary
     */
    public function getDictionary()
    {
        if (!isset($this->dictionary)) {
            $this->setDictionary(new Dictionary());
        }

        return $this->dictionary;
    }

    /**
     * @param Dictionary $dictionary
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
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
}
