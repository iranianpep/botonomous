<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\utility\LoggerUtility;

/**
 * Class LoggerUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class LoggerUtilityTest extends \PHPUnit_Framework_TestCase
{
    const TEST_CHAT_LOG_FILE = 'test_chat_log';
    const TIMEZONE = 'Australia/Melbourne';

    /**
     * Test logChatDisabled.
     */
    public function testLogChatDisabled()
    {
        $config = new Config();
        $config->set('chatLogging', false);
        $config->set('chatLoggingFileName', self::TEST_CHAT_LOG_FILE);

        $utility = new LoggerUtility($config);
        $result = $utility->logChat(__METHOD__, 'test message');

        $this->assertFalse($result);

        $this->removeTestChatLogFile($utility->getLogFilePath());
    }

    /**
     * Test logChatEnabled.
     */
    public function testLogChatEnabled()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', true);
        $config->set('chatLoggingFileName', self::TEST_CHAT_LOG_FILE);

        $utility = new LoggerUtility($config);
        $result = $utility->logChat(__METHOD__, 'test message');

        $this->assertTrue($result);

        $this->removeTestChatLogFile($utility->getLogFilePath());
    }

    /**
     * Test logRaw.
     */
    public function testLogRaw()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', true);
        $config->set('chatLoggingFileName', self::TEST_CHAT_LOG_FILE);

        $utility = new LoggerUtility($config);
        $result = $utility->logChat(__METHOD__, 'this is a raw log');

        $this->assertTrue($result);

        $this->removeTestChatLogFile($utility->getLogFilePath());
    }

    /**
     * @param $filePath
     */
    private function removeTestChatLogFile($filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
