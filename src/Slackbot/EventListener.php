<?php

namespace Slackbot;

use Slackbot\utility\StringUtility;

/**
 * Class EventListener.
 */
class EventListener extends BaseListener
{
    private $token;
    private $teamId;
    private $apiAppId;
    private $event;
    private $requestEventMaps = [
        'ts'       => 'timestamp',
        'event_ts' => 'eventTimestamp',
    ];

    /**
     * EventListener constructor.
     */
    public function __construct()
    {
    }

    /**
     * listen.
     *
     * @return mixed
     */
    public function listen()
    {
        // This is needed for Slash commands, otherwise timeout error is displayed
        $this->respondOK();

        $request = $this->extractRequest();

        if (empty($request)) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        $this->processRequest();
        $this->setRequest($request);

        if ($this->isThisBot() !== false) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return $request;
    }

    /**
     * @return mixed
     */
    public function extractRequest()
    {
        return $this->getRequestUtility()->getPostedBody();
    }

    /**
     * process the event
     */
    public function processRequest()
    {
        $this->loadEvent();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param string $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return string
     */
    public function getApiAppId()
    {
        return $this->apiAppId;
    }

    /**
     * @param string $apiAppId
     */
    public function setApiAppId($apiAppId)
    {
        $this->apiAppId = $apiAppId;
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
            throw new \Exception('Event type must be specified');
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
     * @return array
     */
    public function verifyOrigin()
    {
        $request = $this->getRequest();

        if (!isset($request['token']) || !isset($request['api_app_id'])) {
            return [
                'success' => false,
                'message' => 'Token or api_app_id is not provided',
            ];
        }

        $verificationToken = $this->getConfig()->get('verificationToken');

        if (empty($verificationToken)) {
            throw new \Exception('Verification token must be provided');
        }

        $expectedApiAppId = $this->getConfig()->get('apiAppId');

        if (empty($expectedApiAppId)) {
            throw new \Exception('Api app id must be provided');
        }

        if ($verificationToken === $request['token'] &&
            $expectedApiAppId === $request['api_app_id']) {
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
    public function isThisBot()
    {
        $subType = $this->getRequest('subtype');

        if ($subType === 'bot_message') {
            return true;
        }

        $event = $this->getEvent();

        if ($event instanceof Event && !empty($event->getBotId())) {
            return true;
        }

        return false;
    }
}
