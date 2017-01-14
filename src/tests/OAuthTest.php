<?php

namespace Slackbot;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Slackbot\client\ApiClient;

/**
 * Class OAuthTest.
 * @package Slackbot
 */
class OAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test OAuth.
     */
    public function testOAuth()
    {
        $authorizationUrl = OAuth::AUTHORIZATION_URL;
        $clientId = '4b39e9-752c4';
        $clientSecret = '123456';
        $scope = ['bot', 'users:read'];
        $scopeString = implode(',', $scope);

        $oAuth = new OAuth($clientId, $clientSecret, $scope);
        $addButton = $oAuth->generateAddButton();

        $expected = "<a href='{$authorizationUrl}?scope={$scopeString}&client_id={$clientId}'>
<img  alt='Add to Slack' height='40' width='139'
src='https://platform.slack-edge.com/img/add_to_slack.png'
srcset='https://platform.slack-edge.com/img/add_to_slack.png 1x,
https://platform.slack-edge.com/img/add_to_slack@2x.png 2x' /></a>";

        $this->assertEquals($expected, $addButton);
    }

    /**
     * Test getAccessToken.
     */
    public function testGetAccessToken()
    {
        $accessToken = 'xoxp-XXXXXXXX-XXXXXXXX-XXXXX';

        $clientId = '4b39e9-752c4';
        $clientSecret = '123456';
        $scope = ['bot', 'users:read'];

        $oAuth = new OAuth($clientId, $clientSecret, $scope);
        $oAuth->setAccessToken($accessToken);

        $this->assertEquals($accessToken, $oAuth->getAccessToken('1234'));
    }

    /**
     * Test getAccessToken which includes getAccessToken.
     */
    public function testRequestAccessToken()
    {
        $clientId = '4b39e9-752c4';
        $clientSecret = '123456';
        $scope = ['bot', 'users:read'];

        $oAuth = new OAuth($clientId, $clientSecret, $scope);

        $content = '{
    "access_token": "xoxp-XXXXXXXX-XXXXXXXX-XXXXX",
    "scope": "incoming-webhook,commands,bot",
    "team_name": "Team Installing Your Hook",
    "team_id": "XXXXXXXXXX",
    "incoming_webhook": {
        "url": "https://hooks.slack.com/TXXXXX/BXXXXX/XXXXXXXXXX",
        "channel": "#channel-it-will-post-to",
        "configuration_url": "https://teamname.slack.com/services/BXXXXX"
    },
    "bot":{
        "bot_user_id":"UTTTTTTTTTTR",
        "bot_access_token":"xoxb-XXXXXXXXXXXX-TTTTTTTTTTTTTT"
    }
}';

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

        $oAuth->setApiClient($apiClient);

        $this->assertEquals('xoxp-XXXXXXXX-XXXXXXXX-XXXXX', $oAuth->getAccessToken('1234'));
    }
}
