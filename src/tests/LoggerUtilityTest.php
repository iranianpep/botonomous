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
    const TEST_MESSAGE = 'test message';
    const TEST_RAW_MESSAGE = 'this is a raw log';

    /**
     * Test logChatDisabled.
     */
    public function testLogChatDisabled()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', false);
        $config->set('chatLoggingFileName', self::TEST_CHAT_LOG_FILE);

        $utility = new LoggerUtility($config);

        $this->assertFalse($utility->logChat(__METHOD__, self::TEST_MESSAGE));

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

        $this->assertTrue($utility->logChat(__METHOD__, self::TEST_MESSAGE));

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

        $this->assertTrue($utility->logRaw(self::TEST_RAW_MESSAGE));

        $this->removeTestChatLogFile($utility->getLogFilePath());
    }

    /**
     * Test logRaw.
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

        $utility->logRaw(self::TEST_RAW_MESSAGE);
        // @codeCoverageIgnoreStart
    }

    // @codeCoverageIgnoreEnd

    /**
     * Test logChat.
     */
    public function testLogChatLoggingException()
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

        $utility->logChat(__METHOD__, self::TEST_RAW_MESSAGE);
        // @codeCoverageIgnoreStart
    }

    // @codeCoverageIgnoreEnd

    /**
     * Test logRaw.
     */
    public function testLogRawNotLogging()
    {
        date_default_timezone_set(self::TIMEZONE);

        $config = new Config();
        $config->set('chatLogging', false);

        $utility = new LoggerUtility($config);

        $this->assertFalse($utility->logRaw(self::TEST_RAW_MESSAGE));
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
