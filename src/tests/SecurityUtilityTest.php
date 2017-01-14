<?php

namespace Slackbot\Tests;

use Slackbot\utility\SecurityUtility;

class SecurityUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test generateToken.
     */
    public function testGenerateToken()
    {
        // hash function with the same algorithm always returns a value with the same length
        $securityUtility = new SecurityUtility();
        $token = $securityUtility->generateToken();
        $result = hash($securityUtility->getHashAlgorithm(), 'something dummy');

        $this->assertEquals(strlen($result), strlen($token));
    }

    /**
     * Test getHashAlgorithm.
     *
     * @throws \Exception
     */
    public function testGetHashAlgorithm()
    {
        $securityUtility = new SecurityUtility();

        $this->assertEquals($securityUtility::DEFAULT_HASH_ALGORITHM, $securityUtility->getHashAlgorithm());

        $securityUtility->setHashAlgorithm('sha256');
        $this->assertEquals('sha256', $securityUtility->getHashAlgorithm());

        $this->setExpectedException(
            'Exception',
            'Hash algorithm is not valid'
        );

        $securityUtility->setHashAlgorithm('');
        $this->assertEquals($securityUtility::DEFAULT_HASH_ALGORITHM, $securityUtility->getHashAlgorithm());
    }
}
