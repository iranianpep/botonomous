<?php

namespace Slackbot\Tests;

use Slackbot\SessionHandler;

class SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * SessionHandlerTest constructor.
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Test set.
     */
    public function testSet()
    {
        $sessionHandler = new SessionHandler();
        $sessionHandler->set('testKey', 'testValue');

        $this->assertEquals('testValue', $sessionHandler->get('testKey'));

        $sessionHandler->set('testKey', 'testNewValue');

        $this->assertEquals('testNewValue', $sessionHandler->get('testKey'));
    }

    /**
     * Test get.
     */
    public function testGet()
    {
        $sessionHandler = new SessionHandler();
        $result = $sessionHandler->get('unknownKey');

        $this->assertEquals(null, $result);
    }

    /**
     * Test getSession.
     */
    public function testGetSession()
    {
        $sessionHandler = new SessionHandler();
        $session = $sessionHandler->getSession();

        $session['myKey'] = 'meyValue';
        $sessionHandler->setSession($session);

        $this->assertEquals($session, $sessionHandler->getSession());
    }
}
