<?php

use Slackbot\Config;
use Slackbot\utility\LoggerUtility;

class LoggerUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testLogChat()
    {
        $config = new Config();
        $config->set('chatLogging', false);
        
        $utility = new LoggerUtility($config);
        $result = $utility->logChat(__METHOD__, 'test message');

        $this->assertFalse($result);
    }
}
