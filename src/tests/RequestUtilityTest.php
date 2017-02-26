<?php

namespace Slackbot\Tests;

use Slackbot\utility\RequestUtility;

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
