<?php

namespace Slackbot;

use Slackbot\utility\StringUtility;

/**
 * Class EventListener.
 */
class EventListener
{
    private $request;
    private $token;
    private $teamId;
    private $apiAppId;
    private $event;
    private $requestEventMaps = [
        'ts'       => 'timestamp',
        'event_ts' => 'eventTimestamp',
    ];

    public function __construct($request)
    {
        $this->setRequest($request);
        $this->loadEvent();
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request)
    {
        $this->request = $request;
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
    public function loadEvent()
    {
        $args = $this->getRequest();

        if (!isset($args['type'])) {
            throw new \Exception('Event type must be specified');
        }

        // create the event
        $eventObject = new Event($args['type']);

        // exclude type from the args since it's already passed
        unset($args['type']);

        $stringUtility = new StringUtility();
        foreach ($args as $argKey => $argValue) {
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
}
