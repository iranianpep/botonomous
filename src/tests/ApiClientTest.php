<?php

namespace Slackbot\Tests;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Exception\RequestException;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Handler\MockHandler;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\HandlerStack;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Response;
use Slackbot\client\ApiClient;
use Slackbot\Config;
use Slackbot\Team;

/**
 * Class ApiClientTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getArguments.
     */
    public function testGetArguments()
    {
        $client = new ApiClient();

        $args = [
            'testKey' => 'testValue',
        ];

        $client->setArguments($args);

        $this->assertEquals($args, $client->getArguments());
    }

    /**
     * Test usersList.
     */
    public function testUsersList()
    {
        $this->assertEquals(
            [['id' => 'U023BECGF']],
            $this->getApiClient('{"members": [{"id": "U023BECGF"}]}')->usersList()
        );
    }

    /**
     * Test teamInfo.
     */
    public function testTeamInfo()
    {
        $this->assertEquals(
            ['id' => 'T0LCJF334'],
            $this->getApiClient('{"ok":true,"team":{"id":"T0LCJF334"}}')->teamInfo()
        );
    }

    /**
     * Test teamInfo.
     */
    public function testTeamInfoEmpty()
    {
        $this->assertEquals([], $this->getApiClient('{"ok":true}')->teamInfo());
    }

    /**
     * Test teamInfoAsObject.
     */
    public function testTeamInfoAsObjectEmpty()
    {
        $this->assertEquals(null, $this->getApiClient('{"ok":true}')->teamInfoAsObject());
    }

    /**
     * Test teamInfoAsObject.
     */
    public function testTeamInfoAsObject()
    {
        $teamObject = new Team();
        $teamObject->setSlackId('T0LCJF334');

        $this->assertEquals(
            $teamObject,
            $this->getApiClient('{"ok":true,"team":{"id":"T0LCJF334"}}')->teamInfoAsObject()
        );
    }

    /**
     * @param $content
     *
     * @return ApiClient
     */
    private function getApiClient($content)
    {
        $apiClient = new ApiClient();

        /** @noinspection PhpUndefinedClassInspection */
        $mock = new MockHandler([
            new Response(200, [], $content),
        ]);

        /** @noinspection PhpUndefinedClassInspection */
        $handler = new HandlerStack($mock);
        /** @noinspection PhpUndefinedClassInspection */
        $client = new Client(['handler' => $handler]);

        $apiClient->setClient($client);

        return $apiClient;
    }

    /**
     * @throws \Exception
     */
    public function testApiCallContentException()
    {
        $apiClient = new ApiClient();

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

        // @codeCoverageIgnoreStart
        $apiClient->apiCall('test');
    }

    // @codeCoverageIgnoreEnd

    /**
     * @throws \Exception
     */
    public function testGetArgs()
    {
        $args = (new ApiClient())->getArgs();

        $config = new Config();

        $expected = [
            'token'    => $config->get('apiToken'),
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
        $this->setExpectedException(
            'Exception',
            'channel must be provided for chat.postMessage'
        );

        (new ApiClient())->apiCall('chat.postMessage', []);
    }

    /**
     * @throws \Exception
     */
    public function testApiCallException()
    {
        $apiClient = new ApiClient();

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

        // @codeCoverageIgnoreStart
        $apiClient->apiCall('test');
    }

    // @codeCoverageIgnoreEnd

    /**
     * Test chatPostMessage.
     */
    public function testChatPostMessage()
    {
        $this->setExpectedException(
            'Exception',
            'channel must be provided for chat.postMessage'
        );

        (new ApiClient())->chatPostMessage([]);
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
     * Test test.
     */
    public function testTest()
    {
        $this->assertEquals(
            ['ok' => true],
            $this->getApiClient('{"ok":true}')->test()
        );
    }

    /**
     * Test imList.
     */
    public function testImList()
    {
        $this->assertEquals(
            [['id' => 'D39PQF1C4']],
            $this->getApiClient('{"ok":true,"ims":[{"id":"D39PQF1C4"}]}')->imList()
        );

        $this->assertEmpty($this->getApiClient('{"ok":true}')->imList());
    }

    /**
     * Test validateFields.
     */
    public function testValidateFields()
    {
        $apiClient = new ApiClient();

        $this->setExpectedException(
            '\Exception',
            'client_id must be provided for oauth.access'
        );

        $apiClient->oauthAccess([]);
    }

    /**
     * Test oauthAccess.
     */
    public function testOauthAccess()
    {
        $client = $this->getApiClient('{"access_token": "xoxp-23984754863-2348975623103","scope": "read"}');

        $response = $client->oauthAccess([
            'client_id'     => '4b39e9-752c4',
            'client_secret' => '33fea0113f5b1',
            'code'          => 'ccdaa72ad',
        ]);

        $this->assertEquals(
            [
                'access_token' => 'xoxp-23984754863-2348975623103',
                'scope'        => 'read',
            ],
            $response
        );
    }

    /**
     * Test filterArguments.
     */
    public function testFilterArguments()
    {
        $apiClient = new ApiClient();

        $apiClient->filterArguments('users.list', [
            'token'    => '123',
            'dummyKey' => 'dummyValue',
        ]);
    }
}
