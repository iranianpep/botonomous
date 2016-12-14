<?php

namespace Slackbot\Tests;

use Slackbot\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = new Config();

        $this->assertEquals('testValue', $config->get('testKey'));
    }

    public function testGetWithReplace()
    {
        $config = new Config();

        $this->assertEquals('testValue replaced', $config->get('testKeyReplace', ['replaceIt' => 'replaced']));
    }

    public function testGetExceptException()
    {
        $config = new Config();

        try {
            $config->get('dummyKey');
        } catch (\Exception $e) {
            $this->assertEquals('Key: \'dummyKey\' does not exist in configs', $e->getMessage());
        }
    }
    
    public function testSet()
    {
        $config = new Config();

        $config->set('testKey', 'testNewValue');

        $this->assertEquals('testNewValue', $config->get('testKey'));
    }
}
