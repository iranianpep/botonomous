<?php

namespace Slackbot;

use Slackbot\Tests\PhpunitHelper;

/** @noinspection PhpUndefinedClassInspection */
class BlackListTest extends \PHPUnit_Framework_TestCase
{
    private function getRequest()
    {
        return (new PhpunitHelper())->getRequest();
    }

    public function testIsUsernameBlackListed()
    {
        // set the dummy request
        $request = $this->getRequest();

        $blacklist = new BlackList($request);

        // load the dictionary with dummy data
        $dictionary = new Dictionary();
        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
                    'userId' => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // set the dictionary
        $blacklist->setDictionary($dictionary);

        // since user id is not specified the result is NULL
        $this->assertEmpty($blacklist->isUsernameBlackListed());

        $helper = new PhpunitHelper();
        $dictionaryData = $helper->getDictionaryData('blacklist');

        $dictionary->setData($dictionaryData);

        // user id is not empty
        $this->assertTrue($blacklist->isUsernameBlackListed());

        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
                    'username' => [],
                    'userId'   => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but is empty
        $this->assertFalse($blacklist->isUsernameBlackListed());

        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
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
        $this->assertFalse($blacklist->isUsernameBlackListed());
    }

    public function testIsUserIdBlackListed()
    {
        // set the dummy request
        $request = $this->getRequest();

        $blacklist = new BlackList($request);

        // load the dictionary with dummy data
        $dictionary = new Dictionary();
        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
                    'username' => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // set the dictionary
        $blacklist->setDictionary($dictionary);

        // since user id is not specified the result is NULL
        $this->assertEmpty($blacklist->isUserIdBlackListed());

        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
                    'username' => [],
                    'userId'   => [
                        'dummyUserId',
                    ],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is not empty
        $this->assertTrue($blacklist->isUserIdBlackListed());

        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
                    'username' => [],
                    'userId'   => [],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but is empty
        $this->assertFalse($blacklist->isUserIdBlackListed());

        $dictionaryData = [
            'access-control' => [
                'blacklist' => [
                    'username' => [],
                    'userId'   => [
                        'blahblah',
                    ],
                ],
            ],
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but with a different user id
        $this->assertFalse($blacklist->isUserIdBlackListed());
    }
}
