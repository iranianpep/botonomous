<?php

namespace Slackbot\client;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use Slackbot\Config;
use Slackbot\Team;
use Slackbot\utility\ArrayUtility;
use Slackbot\utility\StringUtility;

/**
 * Class ApiClient.
 */
class ApiClient
{
    const BASE_URL = 'https://slack.com/api/';

    private $arguments = [
        'rtm.start' => [
            'required' => [
                'token',
            ],
            'optional' => [
                'simple_latest',
                'no_unreads',
                'mpim_aware',
            ],
        ],
        'chat.postMessage' => [
            'required' => [
                'token',
                'channel',
                'text',
            ],
            'optional' => [
                'parse',
                'link_names',
                'attachments',
                'unfurl_links',
                'unfurl_media',
                'username',
                'as_user',
                'icon_url',
                'icon_emoji',
            ],
        ],
        'oauth.access' => [
            'required' => [
                'client_id',
                'client_secret',
                'code',
            ],
            'optional' => [
                'redirect_uri',
            ],
        ],
        'team.info' => [
            'required' => [
                'token',
            ],
        ],
        'im.list' => [
            'required' => [
                'token',
            ],
        ],
        'users.list' => [
            'required' => [
                'token',
            ],
            'optional' => [
                'presence',
            ],
        ],
    ];

    private $client;

    /**
     * API CURL Call with post method.
     *
     * @param $method
     * @param array $args
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function apiCall($method, array $args = [])
    {
        $args = array_merge($args, $this->getArgs());

        // check the required arguments are provided
        $this->validateRequiredArguments($method, $args);

        // filter unwanted arguments
        $args = $this->filterArguments($method, $args);

        try {
            /** @noinspection PhpUndefinedClassInspection */
            $request = new Request(
                'POST',
                self::BASE_URL.$method,
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                http_build_query($args)
            );

            $response = $this->getClient()->send($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to send data to the Slack API: '.$e->getMessage());
        }

        $response = json_decode($response->getBody()->getContents(), true);

        if (!is_array($response)) {
            throw new \Exception('Failed to process response from the Slack API');
        }

        return $response;
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getArgs()
    {
        $config = new Config();

        return [
            'token'    => $config->get('apiToken'),
            'username' => $config->get('botUsername'),
            'as_user'  => false,
            'icon_url' => $config->get('iconURL'),
        ];
    }

    /**
     * @param $args
     *
     * @return mixed
     */
    public function chatPostMessage($args)
    {
        return $this->apiCall('chat.postMessage', $args);
    }

    /**
     * @param $args
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function rtmStart($args)
    {
        return $this->apiCall('rtm.start', $args);
    }

    /**
     * @throws \Exception
     *
     * @return array
     * @return Team
     */
    public function teamInfo()
    {
        $teamInfo = $this->apiCall('team.info');

        if (!isset($teamInfo['team'])) {
            return [];
        }

        return $teamInfo['team'];
    }

    /**
     * @return Team|void
     */
    public function teamInfoAsObject()
    {
        $teamInfo = $this->teamInfo();

        if (empty($teamInfo)) {
            return;
        }

        // return as object
        $teamObject = new Team();
        $stringUtility = new StringUtility();

        foreach ($teamInfo as $key => $value) {
            // For id, we cannot use 'set'.$stringUtility->snakeCaseToCamelCase($key) since it's named slackId
            if ($key === 'id') {
                $teamObject->setSlackId($value);
                continue;
            }

            $method = 'set'.$stringUtility->snakeCaseToCamelCase($key);
            $teamObject->$method($value);
        }

        return $teamObject;
    }

    /**
     * List all the Slack users in the team.
     *
     * @return array
     */
    public function usersList()
    {
        $result = $this->apiCall('users.list');

        if (!isset($result['members'])) {
            return [];
        }

        return $result['members'];
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function test()
    {
        return $this->apiCall('api.test');
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function imList()
    {
        $result = $this->apiCall('im.list');

        if (!isset($result['ims'])) {
            return [];
        }

        return $result['ims'];
    }

    /**
     * @param $args
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function oauthAccess($args)
    {
        return $this->apiCall('oauth.access', $args);
    }

    /** @noinspection PhpUndefinedClassInspection
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /** @noinspection PhpUndefinedClassInspection
     * @return Client
     */
    public function getClient()
    {
        if (!isset($this->client)) {
            /* @noinspection PhpUndefinedClassInspection */
            $this->setClient(new Client());
        }

        return $this->client;
    }

    /**
     * @param $method
     * @param $args
     *
     * @throws \Exception
     *
     * @return bool
     */
    private function validateRequiredArguments($method, $args)
    {
        $arguments = $this->getArguments($method);

        if (!empty($arguments['required'])) {
            foreach ($arguments['required'] as $argument) {
                if (!isset($args[$argument]) || empty($args[$argument])) {
                    throw new \Exception("{$argument} must be provided for {$method}");
                }
            }
        }

        return true;
    }

    /**
     * @param null $method
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getArguments($method = null)
    {
        if ($method !== null) {
            if (!isset($this->arguments[$method])) {
                return;
            }

            return $this->arguments[$method];
        }

        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param       $method
     * @param array $args
     *
     * @return array
     */
    public function filterArguments($method, array $args)
    {
        $arguments = $this->getArguments($method);

        if (empty($arguments)) {
            return $args;
        }

        if (!isset($arguments['optional'])) {
            $arguments['optional'] = [];
        }

        $extractedArguments = array_merge($arguments['required'], $arguments['optional']);

        return (new ArrayUtility())->filterArray($args, $extractedArguments);
    }
}
