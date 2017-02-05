<?php

namespace Slackbot\Tests;

use Slackbot\utility\SessionUtility;

class SessionUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * SessionUtilityTest constructor.
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Test set.
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
     */
    public function testGet()
    {
        $sessionUtility = new SessionUtility();

        $this->assertEquals(null, $sessionUtility->get('unknownKey'));
    }

    /**
     * Test getSession.
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
