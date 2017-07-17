<?php

namespace Botonomous\utility;

use Botonomous\CommandContainer;

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
     * @return string
     */
    public function removeMentionedBot($message)
    {
        $userLink = $this->getUserLink();

        return preg_replace("/{$userLink}/", '', $message, 1);
    }

    /**
     * Check if the bot user id is mentioned in the message.
     *
     * @param $message
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isBotMentioned($message)
    {
        $userLink = $this->getUserLink();

        return (new StringUtility())->findInString($userLink, $message, false);
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
        $message = $this->removeMentionedBot($message);

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
     * @return \Botonomous\Command|null
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
     * @throws \Exception
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
     * @return string
     */
    private function getUserLink()
    {
        return $this->linkToUser($this->getConfig()->get('botUserId'));
    }

    /**
     * @param array $keywords
     * @param       $message
     *
     * @return array
     */
    public function keywordPos(array $keywords, $message)
    {
        $found = [];
        if (empty($keywords)) {
            return $found;
        }

        $keywords = (new ArrayUtility())->sortArrayByLength($keywords);
        foreach ($keywords as $keyword) {
            $result = preg_match_all("/\b{$keyword}\b/", $message, $matches, PREG_OFFSET_CAPTURE);

            if ($result && !empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    // check if the keyword does not overlap with one of the already found
                    if ($this->isPositionTaken($found, $match[1]) === false) {
                        $found[$keyword][] = $match[1];
                    }
                }
            }
        }

        return $found;
    }

    public function keywordCount(array $keywords, $message)
    {
        $keysPositions = $this->keywordPos($keywords, $message);

        if (empty($keysPositions)) {
            return;
        }

        foreach ($keysPositions as $key => $positions) {
            $keysPositions[$key] = count($positions);
        }

        return $keysPositions;
    }

    /**
     * @param array $tokensPositions
     * @param       $newPosition
     *
     * @return bool
     */
    private function isPositionTaken(array $tokensPositions, $newPosition)
    {
        if (empty($tokensPositions)) {
            return false;
        }

        foreach ($tokensPositions as $token => $positions) {
            $tokenLength = strlen($token);
            foreach ($positions as $position) {
                if ($this->isPositionIn($newPosition, $position, $tokenLength) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $newPosition
     * @param $position
     * @param $tokenLength
     *
     * @return bool
     */
    private function isPositionIn($newPosition, $position, $tokenLength)
    {
        return $newPosition >= $position && $newPosition < $position + $tokenLength;
    }
}
