<?php

namespace Botonomous;

use Botonomous\client\ApiClientTest;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class EventTest extends TestCase
{
    /**
     * Test getBotId
     */
    public function testGetBotId()
    {
        $event = new Event('message');
        $event->setBotId('B123');

        $this->assertEquals('B123', $event->getBotId());
    }

    /**
     * Test isDirectMessageEmpty
     */
    public function testIsDirectMessageEmpty()
    {
        $event = new Event('message');
        $this->assertEmpty($event->isDirectMessage());
    }

    /**
     * Test isDirectMessageEmpty
     */
    public function testIsDirectMessage()
    {
        $event = new Event('message');
        $event->setChannel('D024BE7RE');

        $apiClientTest = new ApiClientTest();
        $apiClient = $apiClientTest->getApiClient($this->getDummyImListResponse());

        $event->setApiClient($apiClient);

        $this->assertEquals(true, $event->isDirectMessage());
    }

    /**
     * Test isDirectMessageEmpty
     */
    public function testIsNotDirectMessage()
    {
        $event = new Event('message');

        $apiClientTest = new ApiClientTest();
        $apiClient = $apiClientTest->getApiClient($this->getDummyImListResponse());

        $event->setApiClient($apiClient);

        $this->assertEmpty($event->isDirectMessage());
    }

    /**
     * Return im list dummy response content
     */
    private function getDummyImListResponse()
    {
        return '{"ok": true, "ims":[{"id": "D024BFF1M", "is_im": true, "user": "USLACKBOT", "created": 1372105335, 
        "is_user_deleted": false }, { "id": "D024BE7RE", "is_im": true, "user": "U024BE7LH", "created": 1356250715, 
        "is_user_deleted": false}]}';
    }
}
