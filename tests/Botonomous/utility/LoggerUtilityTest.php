<?php

namespace Botonomous\utility;

use Botonomous\Config;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class LoggerUtilityTest extends TestCase
{
    const TEST_LOG_FILE = 'bot-test.log';
    const TEST_MESSAGE = 'test message';
    const TEST_RAW_MESSAGE = 'this is a raw log';

    /**
     * Test logChatDisabled.
     */
    public function testLogChatDisabled()
    {
        $this->setTimezone();

        $config = new Config();
        $config->set(['logger', 'enabled'], false);
        $this->setLogFile();

        $utility = new LoggerUtility($config);

        $this->assertFalse($utility->logChat(__METHOD__, self::TEST_MESSAGE));
    }

    /**
     * Test logChatEnabled.
     */
    public function testLogChatEnabled()
    {
        $this->setTimezone();

        $config = new Config();
        $config->set(['logger', 'enabled'], true);
        $this->setLogFile();

        $utility = new LoggerUtility($config);

        $this->assertTrue($utility->logChat(__METHOD__, self::TEST_MESSAGE));
    }

    /**
     * Test logRaw.
     */
    public function testLogRawLogging()
    {
        $this->setTimezone();

        $config = new Config();
        $config->set(['logger', 'enabled'], true);
        $this->setLogFile();

        $utility = new LoggerUtility($config);

        $this->assertTrue($utility->logInfo(self::TEST_RAW_MESSAGE));
    }

    /**
     * Test logRaw.
     */
    public function testLogRawNotLogging()
    {
        $this->setTimezone();

        $config = new Config();
        $config->set(['logger', 'enabled'], false);

        $utility = new LoggerUtility($config);

        $this->assertFalse($utility->logInfo(self::TEST_RAW_MESSAGE));
    }

    public function setLogFile($name = null)
    {
        if ($name === null) {
            $name = self::TEST_LOG_FILE;
        }

        $config = new Config();
        $config->set(['logger', 'monolog', 'handlers', 'file', 'fileName'], $name);
    }

    private function setTimezone()
    {
        $config = new Config();
        date_default_timezone_set($config->get('timezone'));
    }
}
