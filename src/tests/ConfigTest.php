<?php

namespace Slackbot\Tests;

use PHPUnit\Framework\TestCase;
use Slackbot\Config;

/**
 * Class ConfigTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class ConfigTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGet()
    {
        $config = new Config();
        $config->set('testKey', 'testValue');

        $this->assertEquals('testValue', $config->get('testKey'));
    }

    /**
     * @throws \Exception
     */
    public function testGetWithReplace()
    {
        $config = new Config();
        $config->set('testKeyReplace', 'testValue {replaceIt}');

        $this->assertEquals(
            'testValue replaced',
            $config->get('testKeyReplace', ['replaceIt' => 'replaced'])
        );
    }

    /**
     * Test getExceptException.
     */
    public function testGetExceptException()
    {
        try {
            (new Config())->get('dummyKey');
        } catch (\Exception $e) {
            $this->assertEquals('Key: \'dummyKey\' does not exist in configs', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function testSet()
    {
        $config = new Config();

        $config->set('testKey', 'testNewValue');

        $this->assertEquals('testNewValue', $config->get('testKey'));
    }
}
