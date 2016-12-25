<?php

namespace Slackbot\Tests;

use Slackbot\client\ApiClient;
use Slackbot\Config;

/**
 * Class ApiClientTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     */
    public function testGetArgs()
    {
        $args = (new ApiClient())->getArgs();

        $config = new Config();

        $expected = [
            'token'    => $config->get('apiToken'),
            'channel'  => $config->get('channelName'),
            'username' => $config->get('botUsername'),
            'as_user'  => false,
            'icon_url' => $config->get('iconURL'),
        ];

        $this->assertEquals($expected, $args);
    }

    /**
     * Test apiCall.
     */
    public function testApiCallInvalidAuth()
    {
        $result = (new ApiClient())->apiCall('chat.postMessage', []);

        $this->assertEquals($this->getExpectedInvalidAuth(), $result);
    }

    /**
     * Test chatPostMessage.
     */
    public function testChatPostMessage()
    {
        $result = (new ApiClient())->chatPostMessage([]);

        $this->assertEquals($this->getExpectedInvalidAuth(), $result);
    }

    /**
     * Test usersList.
     */
    public function testUsersList()
    {
        $result = (new ApiClient())->usersList();

        $this->assertEquals([], $result);
    }

    /**
     * @return array
     */
    private function getExpectedInvalidAuth()
    {
        return [
            'ok' => false,
            'error' => 'invalid_auth'
        ];
    }
}
