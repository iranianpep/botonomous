<?php

namespace Slackbot\Tests;

use Slackbot\Dictionary;

/** @noinspection PhpUndefinedClassInspection */
class BlackListTest extends \PHPUnit_Framework_TestCase
{
    public function testIsBlackListedUserEmail()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();
        $blacklist = $this->getBlackList();
        $blacklist->setApiClient($client);

        $blacklist->setRequest([
            'user_id'   => 'U023BECGF',
            'user_name' => 'bobby',
        ]);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'blacklist' => [
                    'userEmail' => ['bobby@slack.com'],
                ],
            ],
        ]);

        $blacklist->setDictionary($dictionary);

        $this->assertEquals(true, $blacklist->isBlackListed());
    }

    private function getBlackList()
    {
        return (new PhpunitHelper())->getBlackList();
    }

    public function testIsEmailBlackListed()
    {
        $blacklist = $this->getBlackList();
        $client = (new PhpunitHelper())->getUserInfoClient();
        $blacklist->setApiClient($client);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'blacklist' => [
                    'userEmail' => [
                        'bobby@slack.com',
                    ],
                ],
            ],
        ]);

        $blacklist->setDictionary($dictionary);

        $blacklist->setRequest([
            'user_id' => 'U023BECGF',
        ]);

        $this->assertEquals(true, $blacklist->isEmailBlackListed());
    }

    public function testIsUsernameBlackListed()
    {
        $inputsOutputs = [
            [
                'input' => [
                    'access-control' => [
                        'blacklist' => [
                            'userId' => [],
                        ],
                    ],
                ],
                'output' => null,
            ],
            [
                'input'  => (new PhpunitHelper())->getDictionaryData('blacklist'),
                'output' => true,
            ],
            [
                'input' => [
                    'access-control' => [
                        'blacklist' => [
                            'username' => [],
                            'userId'   => [],
                        ],
                    ],
                ],
                'output' => false,
            ],
            [
                'input' => [
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
                ],
                'output' => false,
            ],
        ];

        $blacklist = $this->getBlackList();
        $dictionary = new Dictionary();
        foreach ($inputsOutputs as $inputOutput) {
            $dictionary->setData($inputOutput['input']);

            // set the dictionary
            $blacklist->setDictionary($dictionary);

            $this->assertEquals($inputOutput['output'], $blacklist->isUsernameBlackListed());
        }
    }

    public function testIsUserIdBlackListed()
    {
        $inputsOutputs = [
            [
                'input' => [
                    'access-control' => [
                        'blacklist' => [
                            'username' => [],
                        ],
                    ],
                ],
                'output' => null,
            ],
            [
                'input' => [
                    'access-control' => [
                        'blacklist' => [
                            'username' => [],
                            'userId'   => [
                                'dummyUserId',
                            ],
                        ],
                    ],
                ],
                'output' => true,
            ],
            [
                'input' => [
                    'access-control' => [
                        'blacklist' => [
                            'username' => [],
                            'userId'   => [],
                        ],
                    ],
                ],
                'output' => false,
            ],
            [
                'input' => [
                    'access-control' => [
                        'blacklist' => [
                            'username' => [],
                            'userId'   => [
                                'blahblah',
                            ],
                        ],
                    ],
                ],
                'output' => false,
            ],
        ];

        $blacklist = $this->getBlackList();
        $dictionary = new Dictionary();
        foreach ($inputsOutputs as $inputOutput) {
            $dictionary->setData($inputOutput['input']);

            // set the dictionary
            $blacklist->setDictionary($dictionary);

            $this->assertEquals($inputOutput['output'], $blacklist->isUserIdBlackListed());
        }
    }

    public function testIsEmailBlackListedFalse()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();

        $blacklist = $this->getBlackList();
        $blacklist->setApiClient($client);

        $this->assertEquals(false, $blacklist->isEmailBlackListed());
    }

    public function testIsEmailBlackListedEmptyEmailList()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();
        $blacklist = $this->getBlackList();
        $blacklist->setApiClient($client);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'blacklist' => [],
            ],
        ]);

        $blacklist->setDictionary($dictionary);

        $blacklist->setRequest([
            'user_id' => 'U023BECGF',
        ]);

        $this->assertEquals(false, $blacklist->isEmailBlackListed());
    }

    public function testIsBlackListedUserId()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();
        $blacklist = $this->getBlackList();
        $blacklist->setApiClient($client);

        $blacklist->setRequest([
            'user_id'   => 'U023BECGF',
            'user_name' => 'bobby',
        ]);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'blacklist' => [
                    'userId' => ['U023BECGF'],
                ],
            ],
        ]);

        $blacklist->setDictionary($dictionary);

        $this->assertEquals(true, $blacklist->isBlackListed());
    }

    public function testIsBlackListedUsername()
    {
        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'blacklist' => [
                    'username' => ['bobby'],
                ],
            ],
        ]);

        $blacklist = $this->getBlackList();
        $blacklist->setDictionary($dictionary);

        $this->assertEquals(false, $blacklist->isBlackListed());

        $dictionary->setData([
            'access-control' => [
                'blacklist' => [
                    'username' => ['dummyUserName'],
                ],
            ],
        ]);

        $this->assertEquals(true, $blacklist->isBlackListed());
    }

    public function testIsBlackListedFalse()
    {
        $blacklist = $this->getBlackList();
        $this->assertEquals(false, $blacklist->isBlackListed());
    }
}
