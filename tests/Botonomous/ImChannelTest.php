<?php

namespace Botonomous;

use PHPUnit\Framework\TestCase;

/**
 * Class ImChannelTest.
 */
class ImChannelTest extends TestCase
{
    public function testIsIm()
    {
        $imChannel = $this->getImChannel();
        $this->assertFalse($imChannel->isIm());
    }

    public function testIsUserDeleted()
    {
        $imChannel = $this->getImChannel();
        $this->assertTrue($imChannel->isUserDeleted());
    }

    public function testGetCreated()
    {
        $imChannel = $this->getImChannel();
        $this->assertEquals('1372105335', $imChannel->getCreated());
    }

    private function getImChannel()
    {
        $imChannel = new ImChannel();

        $imChannel->setIm(false);
        $imChannel->setUserDeleted(true);
        $imChannel->setCreated('1372105335');

        return $imChannel;
    }
}
