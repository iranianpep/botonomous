<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\utility\LoggerUtility;

class LoggerUtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testLogChatDisabled()
    {
        $config = new Config();
        $config->set('chatLogging', false);
        
        $utility = new LoggerUtility($config);
        $result = $utility->logChat(__METHOD__, 'test message');

        $this->assertFalse($result);
    }
    
    public function testLogChatEnabled()
    {
        date_default_timezone_set('Australia/Melbourne');
        
        $config = new Config();
        $config->set('chatLogging', true);
        $config->set('chatLoggingFileName', 'test_chat_log');

        $utility = new LoggerUtility($config);
        $result = $utility->logChat(__METHOD__, 'test message');

        $this->assertTrue($result);
    }
}
