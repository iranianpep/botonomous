<?php

namespace Botonomous\utility;

use PHPUnit\Framework\TestCase;

class SessionUtilityTest extends TestCase
{
    /**
     * Test set.
     *
     * @runInSeparateProcess
     */
    public function testSet()
    {
        $sessionUtility = new SessionUtility();
        $sessionUtility->set('testKey', 'testValue');

        $this->assertEquals('testValue', $sessionUtility->get('testKey'));

        $sessionUtility->set('testKey', 'testNewValue');

        $this->assertEquals('testNewValue', $sessionUtility->get('testKey'));
    }

    /**
     * Test get.
     *
     * @runInSeparateProcess
     */
    public function testGet()
    {
        $sessionUtility = new SessionUtility();

        $this->assertEquals(null, $sessionUtility->get('unknownKey'));
    }

    /**
     * Test getSession.
     *
     * @runInSeparateProcess
     */
    public function testGetSession()
    {
        $sessionUtility = new SessionUtility();
        $session = $sessionUtility->getSession();

        $session['myKey'] = 'meyValue';
        $sessionUtility->setSession($session);

        $this->assertEquals($session, $sessionUtility->getSession());
    }
}
