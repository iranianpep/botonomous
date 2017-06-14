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
    const TEST_INFO_LOG = 'this is an info log';

    /**
     * Test logChatDisabled.
     */
    public function testLogChatDisabled()
    {
        $this->setTimezone();

        $this->setLogFile();

        $utility = new LoggerUtility($this->getConfig(false));

        $this->assertFalse($utility->logChat(__METHOD__, self::TEST_MESSAGE));
    }

    /**
     * Test logChatEnabled.
     */
    public function testLogChatEnabled()
    {
        $this->setTimezone();

        $this->setLogFile();

        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logChat(__METHOD__, self::TEST_MESSAGE));
    }

    /**
     * Test logRaw.
     */
    public function testLogRawLogging()
    {
        $this->setTimezone();

        $this->setLogFile();

        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logInfo(self::TEST_INFO_LOG));
    }

    /**
     * Test logRaw.
     */
    public function testLogRawNotLogging()
    {
        $this->setTimezone();

        $utility = new LoggerUtility($this->getConfig(false));

        $this->assertFalse($utility->logInfo(self::TEST_INFO_LOG));
    }

    public function setLogFile($name = null)
    {
        if ($name === null) {
            $name = self::TEST_LOG_FILE;
        }

        $config = new Config();
        $config->set(['logger', 'monolog', 'handlers', 'file', 'fileName'], $name);
    }

    public function testGetLogContent()
    {
        $utility = new LoggerUtility($this->getConfig());

        $this->assertEquals(
            __METHOD__.'|test message|#dummy',
            $utility->getLogContent(__METHOD__, 'test message', '#dummy')
        );
    }

    public function testLogDebug()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logDebug('This is a debug log'));
    }

    public function testLogNotice()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logNotice('This is a notice log'));
    }

    public function testLogWarning()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logWarning('This is a warning log'));
    }

    public function testLogError()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logError('This is an error log'));
    }

    public function testLogCritical()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logCritical('This is a critical log'));
    }

    public function testLogAlert()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logAlert('This is an alert log'));
    }

    public function testLogEmergency()
    {
        $this->setLogFile();
        $utility = new LoggerUtility($this->getConfig());

        $this->assertTrue($utility->logEmergency('This is an emergency log'));
    }

    public function testLogInvalidLevel()
    {
        $utility = new LoggerUtility($this->getConfig());

        $this->expectException('\Exception');
        $this->expectExceptionMessage("'invalidLevel' is an invalid log level");

        $utility->log('invalidLevel', 'dummyMessage');
    }

    private function getConfig($loggerEnabled = true)
    {
        $config = new Config();
        $config->set(['logger', 'enabled'], $loggerEnabled === true ? true : false);

        return $config;
    }

    private function setTimezone()
    {
        $config = new Config();
        date_default_timezone_set($config->get('timezone'));
    }
}
