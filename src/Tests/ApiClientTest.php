<?php

namespace Slackbot\Tests;

use Slackbot\client\ApiClient;
use Slackbot\Config;

/**
 * Class ApiClientTest.
 */
class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     */
    public function testGetArgs()
    {
        $apiClient = new ApiClient();
        $args = $apiClient->getArgs();

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
}
