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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user)
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
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getEventTimestamp(): string
    {
        return $this->eventTimestamp;
    }

    /**
     * @param string $eventTimestamp
     */
    public function setEventTimestamp(string $eventTimestamp)
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
    public function setChannel(string $channel)
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
    public function setBotId(string $botId)
    {
        $this->botId = $botId;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient(): ApiClient
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
     * @throws \Exception
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
            /** @var ImChannel $imChannel */
            if ($imChannel->getUser() === 'USLACKBOT') {
                // ignore any direct conversation with the default slack bot
                continue;
            }

            // if IM Object id equals the event channel id the conversation is with the bot
            if ($imChannel->getSlackId() === $this->getChannel()) {
                return true;
            }
        }
    }
}
