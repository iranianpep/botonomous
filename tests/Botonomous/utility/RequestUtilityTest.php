<?php

namespace Botonomous\utility;

use PHPUnit\Framework\TestCase;

class RequestUtilityTest extends TestCase
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
