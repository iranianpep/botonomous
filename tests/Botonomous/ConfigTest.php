<?php

namespace Botonomous;

use PHPUnit\Framework\TestCase;

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

    public function testGetByPlugin()
    {
        $config = new Config();
        $result = $config->get('testConfigKey', [], 'help');

        $this->assertEquals('testConfigValue', $result);
    }

    public function testGetByInvalidPlugin()
    {
        $config = new Config();

        $this->expectException('\Exception');
        $this->expectExceptionMessage(
            "Config file: 'Botonomous\\plugin\\dummy\\DummyConfig.php' does not exist"
        );

        $config->get('testConfigKey', [], 'dummy');
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

        $config->set('testConfigKey', 'testConfigValueEdited', 'help');

        $this->assertEquals('testConfigValueEdited', $config->get('testConfigKey', [], 'help'));

        // reset config
        $config->set('testConfigKey', 'testConfigValue', 'help');
    }

    /**
     * @throws \Exception
     */
    public function testSetExceptException()
    {
        $this->expectException('\Exception');
        $invalidPluginPath = 'Botonomous\plugin\dummyinvalidplugin\DummyInvalidPluginConfig.php';
        $this->expectExceptionMessage(
            "Config file: '{$invalidPluginPath}' does not exist"
        );

        $config = new Config();
        $config->set('testConfigKey', 'testConfigValueEdited', 'dummyInvalidPlugin');
    }
}
