<?php

namespace Slackbot\Tests;

use Slackbot\Event;
use Slackbot\EventListener;

class EventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get event.
     */
    public function testGetEventEmptyEventType()
    {
        $eventListener = new EventListener();
       
        // mock request
        $request = [];

        $eventListener->setRequest($request);

        $this->setExpectedException(
            '\Exception',
            'Event type must be specified'
        );

        $eventListener->getEvent();
    }

    /**
     * Test get event.
     */
    public function testGetEventType()
    {
        $eventListener = new EventListener();

        // mock request
        $eventType = 'message';
        $request = [
            'type' => $eventType
        ];

        $eventListener->setRequest($request);

        $event = $eventListener->getEvent();

        $this->assertEquals($eventType, $event->getType());
    }

    /**
     * Test get event.
     */
    public function testGetAlreadySetEvent()
    {
        $eventType = 'message';
        $existingEvent = new Event($eventType);

        $eventListener = new EventListener();
        $eventListener->setEvent($existingEvent);

        // mock request
        $newRequest = [
            'type' => 'reaction_added'
        ];

        $eventListener->setRequest($newRequest);

        $event = $eventListener->getEvent();

        /**
         * Since the event is already set, the second one is not considered / loaded
         * That's why event type is the same as the first one
         */
        $this->assertEquals($eventType, $event->getType());
    }
}
