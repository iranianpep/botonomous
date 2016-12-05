<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = new \Slackbot\Config();

        $this->assertEquals('testValue', $config->get('testKey'));
    }

    public function testGetExceptException()
    {
        $config = new \Slackbot\Config();

        $this->setExpectedException('Exception', 'Key: \'dummyKey\' does not exist in configs');

        $config->get('dummyKey');
    }
}
