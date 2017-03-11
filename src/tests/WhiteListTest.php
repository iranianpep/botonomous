<?php

namespace Slackbot\Tests;

use Slackbot\client\ApiClient;
use Slackbot\Dictionary;

/** @noinspection PhpUndefinedClassInspection */
class WhiteListTest extends \PHPUnit_Framework_TestCase
{
    private function getWhiteList()
    {
        return (new PhpunitHelper())->getWhiteList();
    }

    public function testGetRequest()
    {
        $whitelist = $this->getWhiteList();

        $this->assertEquals((new PhpunitHelper())->getRequest(), $whitelist->getRequest());

        // overwrite the request
        $whitelist->setRequest([]);

        $this->assertEmpty($whitelist->getRequest());
    }

    public function testGetApiClient()
    {
        $whitelist = $this->getWhiteList();

        $apiClient = new ApiClient();
        $this->assertEquals($apiClient, $whitelist->getApiClient());

        // call it again
        $this->assertEquals($apiClient, $whitelist->getApiClient());
    }

    public function testIsUsernameWhiteListed()
    {
        $whitelist = $this->getWhiteList();

        $inputsOutputs = [
            [
                'input' => [
                    'access-control' => [
                        'whitelist' => [
                            'userId' => [],
                        ],
                    ],
                ],
                'output' => null,
            ],
            [
                'input' => [
                    'access-control' => [
                        'whitelist' => [
                            'userId' => [],
                        ],
                    ],
                ],
                'output' => null,
            ],
            [
                'input'  => (new PhpunitHelper())->getDictionaryData('whitelist'),
                'output' => true,
            ],
            [
                'input' => [
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
                ],
                'output' => false,
            ],
            [
                'input' => [
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
                ],
                'output' => false,
            ],
        ];

        $dictionary = new Dictionary();
        foreach ($inputsOutputs as $inputOutput) {
            $dictionary->setData($inputOutput['input']);

            // set the dictionary
            $whitelist->setDictionary($dictionary);

            $this->assertEquals($inputOutput['output'], $whitelist->isUsernameWhiteListed());
        }
    }

    public function testIsUserIdWhiteListed()
    {
        $inputsOutputs = [
            [
                'input'  => [],
                'output' => null,
            ],
            [
                'input' => [
                    'access-control' => [],
                ],
                'output' => null,
            ],
            [
                'input' => [
                    'access-control' => [
                        'whitelist' => [
                            'username' => [],
                        ],
                    ],
                ],
                'output' => null,
            ],
            [
                'input'=> [
                    'access-control' => [
                        'whitelist' => [
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
                'input'=> [
                    'access-control' => [
                        'whitelist' => [
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
                'input'=> [
                    'access-control' => [
                        'whitelist' => [
                            'username' => [],
                            'userId'   => [],
                        ],
                    ],
                ],
                'output' => false,
            ],
            [
                'input'=> [
                    'access-control' => [
                        'whitelist' => [
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

        $whitelist = $this->getWhiteList();
        $dictionary = new Dictionary();
        foreach ($inputsOutputs as $inputOutput) {
            $dictionary->setData($inputOutput['input']);

            // set the dictionary
            $whitelist->setDictionary($dictionary);

            $this->assertEquals($inputOutput['output'], $whitelist->isUserIdWhiteListed());
        }
    }

    public function testIsEmailWhiteListedFalse()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();

        $whitelist = $this->getWhiteList();
        $whitelist->setApiClient($client);

        $this->assertEquals(false, $whitelist->isEmailWhiteListed());
    }

    public function testIsEmailWhiteListed()
    {
        $whitelist = $this->getWhiteList();

        $client = (new PhpunitHelper())->getUserInfoClient();
        $whitelist->setApiClient($client);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'whitelist' => [
                    'userEmail' => [
                        'bobby@slack.com',
                    ],
                ],
            ],
        ]);

        $whitelist->setDictionary($dictionary);

        $whitelist->setRequest([
            'user_id' => 'U023BECGF',
        ]);

        $this->assertEquals(true, $whitelist->isEmailWhiteListed());
    }

    public function testGetSlackUserInfoEmptyRequest()
    {
        $whitelist = $this->getWhiteList();
        $whitelist->setRequest([]);

        $this->assertEmpty($whitelist->getSlackUserInfo());
    }

    public function testGetSlackUserInfoNotFound()
    {
        $whitelist = $this->getWhiteList();
        $this->assertFalse($whitelist->getSlackUserInfo());
    }

    public function testIsWhiteListed()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();
        $whitelist = $this->getWhiteList();
        $whitelist->setApiClient($client);

        $whitelist->setRequest([
            'user_id'   => 'U023BECGF',
            'user_name' => 'bobby',
        ]);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'whitelist' => [
                    'userId'    => ['U023BECGF'],
                    'username'  => ['bobby'],
                    'userEmail' => ['bobby@slack.com'],
                ],
            ],
        ]);

        $whitelist->setDictionary($dictionary);

        $this->assertEquals(true, $whitelist->isWhiteListed());
    }

    public function testIsWhiteListedFalse()
    {
        $whitelist = $this->getWhiteList();
        $this->assertEquals(false, $whitelist->isWhiteListed());
    }

    public function testIsEmailWhiteListedEmptyEmailList()
    {
        $client = (new PhpunitHelper())->getUserInfoClient();
        $whitelist = $this->getWhiteList();
        $whitelist->setApiClient($client);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'whitelist' => [],
            ],
        ]);

        $whitelist->setDictionary($dictionary);

        $whitelist->setRequest([
            'user_id' => 'U023BECGF',
        ]);

        $this->assertEquals(true, $whitelist->isEmailWhiteListed());
    }
}
