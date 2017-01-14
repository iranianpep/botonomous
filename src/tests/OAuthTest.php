<?php

namespace Slackbot;

/**
 * Class OAuthTest.
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
        $scope = ['bot', 'users:read'];
        $scopeString = implode(',', $scope);

        $oAuth = new OAuth($clientId, $scope);
        $addButton = $oAuth->generateAddButton();

        $expected = "<a href='{$authorizationUrl}?scope={$scopeString}&client_id={$clientId}'>
<img  alt='Add to Slack' height='40' width='139'
src='https://platform.slack-edge.com/img/add_to_slack.png'
srcset='https://platform.slack-edge.com/img/add_to_slack.png 1x,
https://platform.slack-edge.com/img/add_to_slack@2x.png 2x' /></a>";

        $this->assertEquals($expected, $addButton);
    }
}
