<?php

namespace Slackbot\utility;

class MessageUtility
{
    public function removeMentionedBotUsername($message)
    {
        $config = new \Slackbot\Config();
        $botUsername = $config->get('botUsername');
        $mentionedBotUsername = "@{$botUsername}";

        // TODO only remove it from the beginning
        return str_replace($mentionedBotUsername, '', $message);
    }

    /**
     * @param $message
     * @return null
     */
    public function extractCommand($message)
    {
        // remove the bot mention if it exists


        /**
         * Command must start with / and at the beginning of the sentence
         */
        $pattern = '/^(\/\w{1,})/';
        preg_match($pattern, $message, $groups);

        // If command is found, remove / from the beginning of the command
        return isset($groups[1]) ? ltrim($groups[1], '/') : null;
    }
}
