<?php

namespace Botonomous\listener;

use Botonomous\Config;
use Botonomous\Event;
use Botonomous\utility\RequestUtility;
use PHPUnit\Framework\TestCase;

class EventListenerTest extends TestCase
{
    /**
     * Test listen.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testListen()
    {
        $eventListener = new EventListener();
        $config = new Config();
        $eventListener->setConfig($config);

        $requestUtility = $this->getSampleRequestUtility();
        $eventListener->setRequestUtility($requestUtility);

        $this->assertEquals($eventListener->getRequest(), $eventListener->listen());
    }

    /**
     * Test listenBot.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testListenBot()
    {
        $eventListener = new EventListener();
        $config = new Config();
        $eventListener->setConfig($config);

        $requestUtility = $this->getSampleRequestUtility();
        $content = $requestUtility->getContent();

        $content = json_decode($content, true);
        $content['event']['bot_id'] = 'B123';
        $content = json_encode($content);

        $requestUtility->setContent($content);
        $eventListener->setRequestUtility($requestUtility);

        $this->assertEmpty($eventListener->listen());
    }

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

        $this->expectException('\Exception');
        $this->expectExceptionMessage(EventListener::MISSING_EVENT_TYPE_MESSAGE);

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
     * Test get channel id.
     */
    public function testGetChannelId()
    {
        $eventListener = new EventListener();
        $eventListener->setRequestUtility($this->getSampleRequestUtility());

        $this->assertEquals('test', $eventListener->getChannelId());
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
        $config->set('listener', 'event');
        $eventListener = new EventListener();
        $eventListener->setConfig($config);

        $eventListener->setRequest([]);

        $this->assertEquals([
            'success' => false,
            'message' => EventListener::MISSING_TOKEN_OR_APP_ID_MESSAGE,
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
            'api_app_id' => (new Config())->get('appId'),
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
        $config->set('listener', 'event');
        $eventListener = new EventListener();
        $eventListener->setConfig($config);
        $config->set('verificationToken', '');

        $eventListener->setRequest([
            'token'      => '12345',
            'api_app_id' => '12345',
        ]);

        $this->expectException('\Exception');
        $this->expectExceptionMessage(EventListener::MISSING_VERIFICATION_TOKEN_MESSAGE);

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
        $config->set('listener', 'event');
        $eventListener = new EventListener();
        $config->set('appId', '');
        $config->set('verificationToken', '12345');
        $eventListener->setConfig($config);

        $eventListener->setRequest([
            'token'      => '12345',
            'api_app_id' => '12345',
        ]);

        $this->expectException('\Exception');
        $this->expectExceptionMessage(EventListener::MISSING_APP_ID_MESSAGE);

        $this->assertEquals([], $eventListener->verifyOrigin());
    }

    /**
     * Test listen.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
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

        $this->assertEquals($eventListener->getEvent()->getType(), 'message');
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
     * Test getAppId.
     */
    public function testGetAppId()
    {
        $eventListener = new EventListener();
        $eventListener->setAppId('12345');

        $this->assertEquals('12345', $eventListener->getAppId());
    }

    /**
     * Test isThisBot.
     */
    public function testIsThisBot()
    {
        $eventListener = new EventListener();
        $this->assertEmpty($eventListener->isThisBot());

        // mock request
        $requestUtility = new RequestUtility();

        $request = [
            'token'      => 'XXYYZZ',
            'team_id'    => 'TXXXXXXXX',
            'api_app_id' => 'AXXXXXXXXX',
            'event'      => [
                'type'     => 'message',
            ],
            'subtype'  => 'bot_message',
        ];

        $requestUtility->setContent(json_encode($request));
        $eventListener->setRequestUtility($requestUtility);

        $this->assertTrue($eventListener->isThisBot());
    }
}
