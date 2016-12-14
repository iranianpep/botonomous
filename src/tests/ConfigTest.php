<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = new \Slackbot\Config();

        $this->assertEquals('testValue', $config->get('testKey'));
    }

    public function testGetWithReplace()
    {
        $config = new \Slackbot\Config();

        $this->assertEquals('testValue replaced', $config->get('testKeyReplace', ['replaceIt' => 'replaced']));
    }

    public function testGetExceptException()
    {
        $config = new \Slackbot\Config();

        try {
            $config->get('dummyKey');
        } catch (Exception $e) {
            $this->assertEquals('Key: \'dummyKey\' does not exist in configs', $e->getMessage());
        }
    }
}
