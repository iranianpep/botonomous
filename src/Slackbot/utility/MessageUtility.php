<?php

namespace Slackbot\utility;

use Slackbot\CommandContainer;
use Slackbot\Config;

/**
 * Class MessageUtility.
 */
class MessageUtility extends AbstractUtility
{
    private $config;

    /**
     * Remove the mentioned bot username from the message.
     *
     * @param $message
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function removeMentionedBotUsername($message)
    {
        $botUsername = $this->getConfig()->get('botUsername');
        $mentionedBotUsername = "@{$botUsername}";

        return str_replace($mentionedBotUsername, '', $message);
    }

    /**
     * Return command name in the message.
     *
     * @param $message
     *
     * @return null|string
     */
    public function extractCommandName($message)
    {
        // remove the bot mention if it exists
        $message = $this->removeMentionedBotUsername($message);

        /**
         * Command must start with / and at the beginning of the sentence.
         */
        $commandPrefix = $this->getConfig()->get('commandPrefix');
        $commandPrefix = preg_quote($commandPrefix, '/');

        $pattern = '/^('.$commandPrefix.'\w{1,})/';

        preg_match($pattern, ltrim($message), $groups);

        // If command is found, remove command prefix from the beginning of the command
        return isset($groups[1]) ? ltrim($groups[1], $commandPrefix) : null;
    }

    /**
     * Return command details in the message.
     *
     * @param $message
     *
     * @return null
     */
    public function extractCommandDetails($message)
    {
        // first get the command name
        $command = $this->extractCommandName($message);

        // then get the command details
        return (new CommandContainer())->getAsObject($command);
    }

    /**
     * @param $triggerWord
     * @param $message
     *
     * @return string
     */
    public function removeTriggerWord($message, $triggerWord)
    {
        $count = 1;

        return ltrim(str_replace($triggerWord, '', $message, $count));
    }

    /**
     * @param        $userId
     * @param string $userName
     *
     * @return string
     */
    public function linkToUser($userId, $userName = '')
    {
        if (empty($userId)) {
            throw new \Exception('User id is not provided');
        }

        if (!empty($userName)) {
            $userName = "|{$userName}";
        }

        return "<@{$userId}{$userName}>";
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!isset($this->config)) {
            $this->setConfig((new Config()));
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
