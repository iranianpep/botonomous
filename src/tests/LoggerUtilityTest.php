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

        $this->assertFalse($utility->logChat(__METHOD__, 'test message'));

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

        $this->assertTrue($utility->logChat(__METHOD__, 'test message'));

        $this->removeTestChatLogFile($utility->getLogFilePath());
    }

    /**
     * Test logRaw.
     */
    public function testLogRawLogging()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', true);
        $config->set('chatLoggingFileName', self::TEST_CHAT_LOG_FILE);

        $utility = new LoggerUtility($config);

        $this->assertTrue($utility->logRaw('this is a raw log'));

        $this->removeTestChatLogFile($utility->getLogFilePath());
    }

    /**
     * Test logRaw.
     *
     * @throws \Exception
     */
    public function testLogRawLoggingException()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', true);
        $config->set('chatLoggingFileName', self::TEST_CHAT_LOG_FILE);

        $utility = new LoggerUtility($config);
        $utility->setLogFilePath('dummy/file/path');

        $this->setExpectedException(
            '\Exception',
            'Failed to write to the log file'
        );

        $this->assertTrue($utility->logRaw('this is a raw log'));
    }

    /**
     * Test logRaw.
     */
    public function testLogRawNotLogging()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', false);

        $utility = new LoggerUtility($config);

        $this->assertFalse($utility->logRaw('this is a raw log'));
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
