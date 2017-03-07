<?php

namespace Slackbot;

use Slackbot\Tests\PhpunitHelper;

/** @noinspection PhpUndefinedClassInspection */
class WhiteListTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRequest()
    {
        $whitelist = (new PhpunitHelper())->getWhiteList();

        $this->assertEquals((new PhpunitHelper())->getRequest(), $whitelist->getRequest());

        // overwrite the request
        $whitelist->setRequest([]);

        $this->assertEmpty($whitelist->getRequest());
    }

    public function testIsUsernameWhiteListed()
    {
        $whitelist = (new PhpunitHelper())->getWhiteList();

        // load the dictionary with dummy data
        $dictionary = new Dictionary();
        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'userId' => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // set the dictionary
        $whitelist->setDictionary($dictionary);

        // since user id is not specified the result is NULL
        $this->assertEmpty($whitelist->isUsernameWhiteListed());

        $helper = new PhpunitHelper();
        $dictionaryData = $helper->getDictionaryData('whitelist');

        $dictionary->setData($dictionaryData);

        // user id is not empty
        $this->assertTrue($whitelist->isUsernameWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [],
                    'userId'   => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but is empty
        $this->assertFalse($whitelist->isUsernameWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [
                        'blahblah',
                    ],
                    'userId' => [
                        'blahblah',
                    ],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but with a different user id
        $this->assertFalse($whitelist->isUsernameWhiteListed());
    }

    public function testIsUserIdWhiteListed()
    {
        $whitelist = (new PhpunitHelper())->getWhiteList();

        // load the dictionary with dummy data
        $dictionary = new Dictionary();
        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // set the dictionary
        $whitelist->setDictionary($dictionary);

        // since user id is not specified the result is NULL
        $this->assertEmpty($whitelist->isUserIdWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [],
                    'userId'   => [
                        'dummyUserId',
                    ],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is not empty
        $this->assertTrue($whitelist->isUserIdWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [],
                    'userId'   => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but is empty
        $this->assertFalse($whitelist->isUserIdWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [],
                    'userId'   => [
                        'blahblah',
                    ],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but with a different user id
        $this->assertFalse($whitelist->isUserIdWhiteListed());
    }
}
