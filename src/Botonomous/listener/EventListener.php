<?php

namespace Botonomous\listener;

use Botonomous\BotonomousException;
use Botonomous\Event;
use Botonomous\utility\StringUtility;

/**
 * Class EventListener.
 */
class EventListener extends AbstractBaseListener
{
    const KEY = 'event';
    const MISSING_TOKEN_OR_APP_ID_MESSAGE = 'Token or api_app_id is not provided';
    const MISSING_APP_ID_MESSAGE = 'Api app id must be provided';
    const MISSING_VERIFICATION_TOKEN_MESSAGE = 'Verification token must be provided';
    const MISSING_EVENT_TYPE_MESSAGE = 'Event type must be specified';

    private $token;
    private $teamId;
    private $appId;
    private $event;
    private $requestEventMaps = [
        'ts'       => 'timestamp',
        'event_ts' => 'eventTimestamp',
    ];

    /**
     * @return mixed
     */
    public function extractRequest()
    {
        return $this->getRequestUtility()->getPostedBody();
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getTeamId(): string
    {
        return $this->teamId;
    }

    /**
     * @param string $teamId
     */
    public function setTeamId(string $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        if (!isset($this->event)) {
            $this->loadEvent();
        }

        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @throws \Exception
     */
    private function loadEvent()
    {
        $request = $this->getRequest();
        if (!isset($request['event'])) {
            return;
        }

        $request = $request['event'];
        if (!isset($request['type'])) {
            throw new BotonomousException(self::MISSING_EVENT_TYPE_MESSAGE);
        }

        // create the event
        $eventObject = new Event($request['type']);

        // exclude type from the args since it's already passed
        unset($request['type']);

        $stringUtility = new StringUtility();
        foreach ($request as $argKey => $argValue) {
            if (array_key_exists($argKey, $this->requestEventMaps)) {
                $argKey = $this->requestEventMaps[$argKey];
            }

            $setterName = 'set'.$stringUtility->snakeCaseToCamelCase($argKey);

            // ignore calling the method if setter does not exist
            if (!method_exists($eventObject, $setterName)) {
                continue;
            }

            $eventObject->$setterName($argValue);
        }

        // set it
        $this->setEvent($eventObject);
    }

    /**
     * @throws \Exception
     *
     * @return array<string,boolean|string>
     */
    public function verifyOrigin()
    {
        $request = $this->getRequest();

        if (!isset($request['token']) || !isset($request['api_app_id'])) {
            return [
                'success' => false,
                'message' => self::MISSING_TOKEN_OR_APP_ID_MESSAGE,
            ];
        }

        $verificationToken = $this->getConfig()->get('verificationToken');

        if (empty($verificationToken)) {
            throw new BotonomousException('Verification token must be provided');
        }

        $expectedAppId = $this->getConfig()->get('appId');

        if (empty($expectedAppId)) {
            throw new BotonomousException(self::MISSING_APP_ID_MESSAGE);
        }

        if ($verificationToken === $request['token'] &&
            $expectedAppId === $request['api_app_id']) {
            return [
                'success' => true,
                'message' => 'O La la!',
            ];
        }

        return [
            'success' => false,
            'message' => 'Token or api_app_id mismatch',
        ];
    }

    /**
     * Check if the request belongs to the bot itself.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isThisBot(): bool
    {
        $subType = $this->getRequest('subtype');

        if ($subType === 'bot_message') {
            return true;
        }

        $event = $this->getEvent();

        return $event instanceof Event && !empty($event->getBotId());
    }

    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->getEvent()->getChannel();
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::KEY;
    }
}
