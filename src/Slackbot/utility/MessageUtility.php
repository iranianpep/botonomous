<?php

namespace Slackbot\utility;

use Slackbot\CommandContainer;

/**
 * Class MessageUtility.
 */
class MessageUtility extends AbstractUtility
{
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
        $config = $this->getConfig();

        $botUsername = $config->get('botUsername');
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
        $pattern = '/^(\/\w{1,})/';
        preg_match($pattern, ltrim($message), $groups);

        // If command is found, remove / from the beginning of the command
        return isset($groups[1]) ? ltrim($groups[1], '/') : null;
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
    public function removeTriggerWord($triggerWord, $message)
    {
        $count = 1;

        return ltrim(str_replace($triggerWord, '', $message, $count));
    }
}
