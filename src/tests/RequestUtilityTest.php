<?php

namespace Slackbot\utility;

class RequestUtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServerProtocol()
    {
        $requestUtility = new RequestUtility();

        if (!isset($_SERVER['SERVER_PROTOCOL'])) {
            $_SERVER['SERVER_PROTOCOL'] = null;
        }

        $this->assertEquals($_SERVER['SERVER_PROTOCOL'], $requestUtility->getServerProtocol());
    }
}
