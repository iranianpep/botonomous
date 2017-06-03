<?php

namespace Botonomous;

use Botonomous\client\ApiClient;

class Event extends AbstractBaseSlack
{
    private $type;
    private $user;
    private $text;
    private $timestamp;
    private $eventTimestamp;
    private $channel;
    private $botId;

    /**
     * Dependencies.
     */
    private $apiClient;

    /**
     * Event constructor.
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->setType($type);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getEventTimestamp()
    {
        return $this->eventTimestamp;
    }

    /**
     * @param string $eventTimestamp
     */
    public function setEventTimestamp($eventTimestamp)
    {
        $this->eventTimestamp = $eventTimestamp;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getBotId()
    {
        return $this->botId;
    }

    /**
     * @param string $botId
     */
    public function setBotId($botId)
    {
        $this->botId = $botId;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        if (!isset($this->apiClient)) {
            $this->setApiClient(new ApiClient());
        }

        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Check if the event belongs to a direct message.
     *
     * @return bool|void
     */
    public function isDirectMessage()
    {
        $imChannels = $this->getApiClient()->imListAsObject();

        if (empty($imChannels)) {
            return;
        }

        foreach ($imChannels as $imChannel) {
            if (!$imChannel instanceof ImChannel) {
                continue;
            }

            // ignore any direct conversation with the default slack bot
            if ($imChannel->getUser() === 'USLACKBOT') {
                continue;
            }

            // if IM Object id equals the event channel id the conversation is with the bot
            if ($imChannel->getSlackId() === $this->getChannel()) {
                return true;
            }
        }
    }
}
