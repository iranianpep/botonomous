<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\Event;
use Slackbot\EventListener;
use Slackbot\utility\RequestUtility;

class EventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return RequestUtility
     */
    private function getSampleRequestUtility()
    {
        // mock request
        $requestUtility = new RequestUtility();

        $request = [
            'token'      => 'XXYYZZ',
            'team_id'    => 'TXXXXXXXX',
            'api_app_id' => 'AXXXXXXXXX',
            'event'      => [
                'type'     => 'message',
                'channel'  => 'test',
                'text'     => 'test',
                'ts'       => '1234567890',
                'event_ts' => '1234567890.123456',
                'user'     => 'UXXXXXXX1',
                'testKey'  => 'testValue',
            ],
        ];

        $requestUtility->setContent(json_encode($request));

        return $requestUtility;
    }

    /**
     * Test get event.
     */
    public function testGetEventEmptyEventType()
    {
        $requestUtility = new RequestUtility();

        $request = [
            'token'      => 'XXYYZZ',
            'team_id'    => 'TXXXXXXXX',
            'api_app_id' => 'AXXXXXXXXX',
            'event'      => [
                'event_ts' => '1234567890.123456',
                'user'     => 'UXXXXXXX1',
            ],
        ];

        $requestUtility->setContent(json_encode($request));

        $eventListener = new EventListener();
        $eventListener->setRequestUtility($requestUtility);

        $this->setExpectedException(
            'Exception',
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
        $eventListener->setRequestUtility($this->getSampleRequestUtility());

        $event = $eventListener->getEvent();

        $this->assertEquals('message', $event->getType());
    }

    /**
     * Test get event.
     */
    public function testGetEvent()
    {
        $eventListener = new EventListener();
        $eventListener->setRequestUtility($this->getSampleRequestUtility());

        $event = $eventListener->getEvent();

        $expected = [
            'type'     => 'message',
            'channel'  => 'test',
            'text'     => 'test',
            'ts'       => '1234567890',
            'event_ts' => '1234567890.123456',
            'user'     => 'UXXXXXXX1',
        ];

        $this->assertEquals($expected, [
            'type'     => $event->getType(),
            'channel'  => $event->getChannel(),
            'user'     => $event->getUser(),
            'text'     => $event->getText(),
            'ts'       => $event->getTimestamp(),
            'event_ts' => $event->getEventTimestamp(),
        ]);
    }

    /**
     * Test get event.
     */
    public function testGetEmptyEvent()
    {
        // mock request
        $requestUtility = new RequestUtility();

        $request = [
            'token'      => 'XXYYZZ',
            'team_id'    => 'TXXXXXXXX',
            'api_app_id' => 'AXXXXXXXXX',
        ];

        $requestUtility->setContent(json_encode($request));

        $eventListener = new EventListener();
        $eventListener->setRequestUtility($requestUtility);

        $this->assertEmpty($eventListener->getEvent());
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

        $eventListener->setRequestUtility($this->getSampleRequestUtility());

        $event = $eventListener->getEvent();

        /*
         * Since the event is already set, the second one is not considered / loaded
         * That's why event type is the same as the first one
         */
        $this->assertEquals($eventType, $event->getType());
    }

    /**
     * Test verifyOrigin.
     *
     * @throws \Exception
     */
    public function testVerifyOrigin()
    {
        $config = new Config();
        $config->set('listenerType', 'event');
        $eventListener = new EventListener();
        $eventListener->setConfig($config);

        $eventListener->setRequest([]);

        $this->assertEquals([
            'success' => false,
            'message' => 'Token or api_app_id is not provided',
        ], $eventListener->verifyOrigin());

        $eventListener->setRequest([
            'token'      => '12345',
            'api_app_id' => '12345',
        ]);

        $this->assertEquals([
            'success' => false,
            'message' => 'Token or api_app_id mismatch',
        ], $eventListener->verifyOrigin());

        $eventListener->setRequest([
            'token'      => (new Config())->get('verificationToken'),
            'api_app_id' => (new Config())->get('apiAppId'),
        ]);

        $this->assertEquals([
            'success' => true,
            'message' => 'O La la!',
        ], $eventListener->verifyOrigin());
    }

    /**
     * Test verifyOrigin.
     *
     * @throws \Exception
     */
    public function testVerifyOriginTokenException()
    {
        $config = new Config();
        $config->set('listenerType', 'event');
        $eventListener = new EventListener();
        $eventListener->setConfig($config);
        $config->set('verificationToken', '');

        $eventListener->setRequest([
            'token'      => '12345',
            'api_app_id' => '12345',
        ]);

        $this->setExpectedException(
            '\Exception',
            'Verification token must be provided'
        );

        $this->assertEquals([], $eventListener->verifyOrigin());
    }

    /**
     * Test verifyOrigin.
     *
     * @throws \Exception
     */
    public function testVerifyOriginAppIdException()
    {
        $config = new Config();
        $config->set('listenerType', 'event');
        $eventListener = new EventListener();
        $config->set('apiAppId', '');
        $config->set('verificationToken', '12345');
        $eventListener->setConfig($config);

        $eventListener->setRequest([
            'token'      => '12345',
            'api_app_id' => '12345',
        ]);

        $this->setExpectedException(
            '\Exception',
            'Api app id must be provided'
        );

        $this->assertEquals([], $eventListener->verifyOrigin());
    }

    /**
     * Test listen.
     */
    public function testListenEmpty()
    {
        $this->assertEmpty((new EventListener())->listen());
    }

    /**
     * Test extractListen.
     */
    public function testExtractRequest()
    {
        $this->assertEmpty((new EventListener())->extractRequest());
    }

    /**
     * Test processRequest.
     */
    public function testProcessRequest()
    {
        $eventListener = new EventListener();
        $eventListener->setRequestUtility($this->getSampleRequestUtility());
        $eventListener->processRequest();

        $this->assertEquals($eventListener->getEvent()->getType(), 'message');
    }

    /**
     * Test processRequest.
     */
    public function testProcessRequestWithChallenge()
    {
        $eventListener = new EventListener();

        // mock request
        $requestUtility = new RequestUtility();

        $challenge = '3eZbrw1aBm2rZgRNFdxV2595E9CY3gmdALWMmHkvFXO7tYXAYM8P';
        $request = [
            'token'     => 'Jhj5dZrVaK7ZwHHjRyZWjbDl',
            'challenge' => $challenge,
            'type'      => 'url_verification',
        ];

        $requestUtility->setContent(json_encode($request));

        $eventListener->setRequestUtility($requestUtility);

        $this->expectOutputString($challenge);

        $eventListener->processRequest();
    }

    /**
     * Test getToken.
     */
    public function testGetToken()
    {
        $eventListener = new EventListener();
        $eventListener->setToken('12345');

        $this->assertEquals('12345', $eventListener->getToken());
    }

    /**
     * Test getTeamId.
     */
    public function testGetTeamId()
    {
        $eventListener = new EventListener();
        $eventListener->setTeamId('12345');

        $this->assertEquals('12345', $eventListener->getTeamId());
    }

    /**
     * Test getApiAppId.
     */
    public function testGetApiAppId()
    {
        $eventListener = new EventListener();
        $eventListener->setApiAppId('12345');

        $this->assertEquals('12345', $eventListener->getApiAppId());
    }

    /**
     * Test isThisBot.
     */
    public function testIsThisBot()
    {
        $eventListener = new EventListener();
        $this->assertEmpty($eventListener->isThisBot());
    }
}
