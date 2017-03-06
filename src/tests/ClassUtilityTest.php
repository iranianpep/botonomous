<?php

namespace Slackbot\utility;

class ClassUtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractClassNameFromFullName()
    {
        $utility = new ClassUtility();
        $this->assertEquals('WhiteList', $utility->extractClassNameFromFullName('Slackbot\WhiteList'));

        $this->assertEquals('WhiteList', $utility->extractClassNameFromFullName('WhiteList'));

        $this->assertEquals('test', $utility->extractClassNameFromFullName('Slackbot\WhiteList\test'));

        $this->assertEquals('', $utility->extractClassNameFromFullName(''));
    }
}
