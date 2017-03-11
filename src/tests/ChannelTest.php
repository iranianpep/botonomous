<?php

namespace Slackbot\Tests;

use Slackbot\Channel;

class ChannelTest extends \PHPUnit_Framework_TestCase
{
    const CHANNEL_ID = 'C0LCK9DH6';
    const CHANNEL_NAME = 'general';

    public function testGetSlackId()
    {
        $channel = new Channel();
        $channel->setSlackId(self::CHANNEL_ID);

        $this->assertEquals(self::CHANNEL_ID, $channel->getSlackId());
    }

    public function testGetName()
    {
        $channel = new Channel();
        $channel->setName(self::CHANNEL_NAME);

        $this->assertEquals(self::CHANNEL_NAME, $channel->getName());
    }
}
