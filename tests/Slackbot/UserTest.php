<?php

namespace Slackbot;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    const USER_ID = 'C0LCK9DH6';
    const USER_NAME = 'general';

    public function testGetSlackId()
    {
        $channel = new User();
        $channel->setSlackId(self::USER_ID);

        $this->assertEquals(self::USER_ID, $channel->getSlackId());
    }

    public function testGetName()
    {
        $channel = new User();
        $channel->setName(self::USER_NAME);

        $this->assertEquals(self::USER_NAME, $channel->getName());
    }
}
