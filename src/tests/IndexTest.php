<?php

namespace Slackbot\Tests;

/**
 * Class IndexTest.
 */
use Slackbot\Config;

/** @noinspection PhpUndefinedClassInspection */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $config = new Config();
        $data = http_build_query([
            'token' => $config->get('outgoingWebhookToken'),
            'text' => '/ping',
            'debug' => true
        ]);

        $url = $config->get('baseUrl').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'index.php';

        $connection = curl_init($url);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($connection);
        curl_close($connection);

        $this->assertEquals('{"text":"pong"}', $result);
    }
}
