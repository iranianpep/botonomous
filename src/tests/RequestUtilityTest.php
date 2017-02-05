<?php

namespace Slackbot\utility;

class RequestUtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServerProtocol()
    {
        $requestUtility = new RequestUtility();

        $this->assertEquals(
            filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING),
            $requestUtility->getServerProtocol()
        );
    }
}
