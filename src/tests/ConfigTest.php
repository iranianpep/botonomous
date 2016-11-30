<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = new \Slackbot\Config();

        $this->assertEquals('https://slack.com/api/chat.postMessage', $config->get('endPoint'));
    }
}
