<?php

namespace Slackbot\Tests;

use Slackbot\Config;

/**
 * Class ConfigTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     */
    public function testGet()
    {
        $this->assertEquals('testValue', (new Config())->get('testKey'));
    }

    /**
     * @throws \Exception
     */
    public function testGetWithReplace()
    {
        $this->assertEquals(
            'testValue replaced',
            (new Config())->get('testKeyReplace', ['replaceIt' => 'replaced'])
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
