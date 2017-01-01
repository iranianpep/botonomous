<?php

namespace Slackbot\Tests;

use /** @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /** @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Exception\RequestException;
use /** @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Handler\MockHandler;
use /** @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\HandlerStack;
use /** @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use /** @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Response;
use Slackbot\client\ApiClient;
use Slackbot\Config;

/**
 * Class ApiClientTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test usersList.
     */
    public function testUsersList()
    {
        $apiClient = new ApiClient();

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedClassInspection */
        $mock = new MockHandler([
            new Response(200, [], '{"members": [{"id": "U023BECGF"}]}'),
        ]);

        /** @noinspection PhpUndefinedClassInspection */
        $handler = new HandlerStack($mock);
        /** @noinspection PhpUndefinedClassInspection */
        $client = new Client(['handler' => $handler]);

        $apiClient->setClient($client);

        $this->assertEquals([['id' => 'U023BECGF']], $apiClient->usersList());
    }

    /**
     * @throws \Exception
     */
    public function testApiCallContentException()
    {
        $apiClient = new ApiClient();

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedClassInspection */
        $mock = new MockHandler([
            new Response(200, [], 'test'),
        ]);

        /** @noinspection PhpUndefinedClassInspection */
        $handler = new HandlerStack($mock);
        /** @noinspection PhpUndefinedClassInspection */
        $client = new Client(['handler' => $handler]);

        $apiClient->setClient($client);

        $this->setExpectedException(
            '\Exception',
            'Failed to process response from the Slack API'
        );

        $apiClient->apiCall('test');
    }

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
     * @throws \Exception
     */
    public function testApiCallException()
    {
        $apiClient = new ApiClient();

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedClassInspection */
        $mock = new MockHandler([
            new RequestException('Error Communicating with Server', new Request('Post', $apiClient::BASE_URL.'test')),
        ]);

        /** @noinspection PhpUndefinedClassInspection */
        $handler = new HandlerStack($mock);
        /** @noinspection PhpUndefinedClassInspection */
        $client = new Client(['handler' => $handler]);

        $apiClient->setClient($client);

        $this->setExpectedException(
            '\Exception',
            'Failed to send data to the Slack API: Error Communicating with Server'
        );

        $apiClient->apiCall('test');
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
    public function testUsersListEmpty()
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
            'ok'    => false,
            'error' => 'invalid_auth',
        ];
    }
}
