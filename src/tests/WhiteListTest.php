<?php

namespace Slackbot;

/** @noinspection PhpUndefinedClassInspection */
class WhiteListTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRequest()
    {
        $request = [
            'user_name' => 'dummyUserName',
            'user_id'   => 'dummyUserId',
        ];

        $whitelist = new WhiteList($request);

        $this->assertEquals($request, $whitelist->getRequest());

        // overwrite the request
        $whitelist->setRequest([]);

        $this->assertEmpty($whitelist->getRequest());
    }

    public function testIsUsernameWhiteListed()
    {
        // set the dummy request
        $request = [
            'user_name' => 'dummyUserName',
            'user_id'   => 'dummyUserId',
        ];

        $whitelist = new WhiteList($request);

        // load the dictionary with dummy data
        $dictionary = new Dictionary();
        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
<<<<<<< HEAD
                    'userId' => []
                ],
            ]
        ];

        $dictionary->setData($dictionaryData);

        // set the dictionary
        $whitelist->setDictionary($dictionary);

        // since user id is not specified the result is NULL
        $this->assertEmpty($whitelist->isUsernameWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [
                        'dummyUserName'
                    ],
                    'userId' => [
                        'dummyUserId'
                    ]
                ],
            ]
        ];

        $dictionary->setData($dictionaryData);

        // user id is not empty
        $this->assertTrue($whitelist->isUsernameWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [],
                    'userId' => []
                ],
            ]
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but is empty
        $this->assertFalse($whitelist->isUsernameWhiteListed());

        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => [
                        'blahblah'
                    ],
                    'userId' => [
                        'blahblah'
                    ]
                ],
            ]
        ];

        $dictionary->setData($dictionaryData);

        // user id is set but with a different user id
        $this->assertFalse($whitelist->isUsernameWhiteListed());
    }

    public function testIsUserIdWhiteListed()
    {
        // set the dummy request
        $request = [
            'user_name' => 'dummyUserName',
            'user_id' => 'dummyUserId'
        ];

        $whitelist = new WhiteList($request);

        // load the dictionary with dummy data
        $dictionary = new Dictionary();
        $dictionaryData = [
            'access-control' => [
                'whitelist' => [
                    'username' => []
=======
                    'username' => [],
>>>>>>> 14f970a33f610290373abfcd8e34e48fb0f64312
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
