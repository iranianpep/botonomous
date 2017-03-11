<?php

namespace Slackbot\Tests;

use Slackbot\User;

class UserTest extends \PHPUnit_Framework_TestCase
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
