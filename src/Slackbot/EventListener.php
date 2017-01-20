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

    public function __construct()
    {
    }

    public function listen()
    {
        if ($this->extractRequest() === true) {
            $this->processRequest();
        }
    }

    private function extractRequest()
    {
        $requestBody = file_get_contents('php://input');

        if (empty($requestBody)) {
            return;
        }

        $this->setRequest(json_decode($requestBody, true));

        return true;
    }

    public function processRequest()
    {
        // Slack recommends responding to events with a HTTP 200 OK ASAP
        header("HTTP/1.1 200 OK");
        header("Content-type:application/x-www-form-urlencoded");

        $request = $this->getRequest();

        // in case URL verification handshake is required
        if (!empty($request['challenge'])) {
            echo $request['challenge'];
        } else {
            // process the event
            $this->loadEvent();
        }
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
    public function loadEvent()
    {
        $request = $this->getRequest();

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
}
