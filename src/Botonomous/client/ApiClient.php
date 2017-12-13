<?php

namespace Botonomous\client;

use Botonomous\BotonomousException;
use Botonomous\ImChannel;
use Botonomous\Team;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiClient.
 */
class ApiClient extends AbstractClient
{
    const BASE_URL = 'https://slack.com/api/';
    const CONTENT_TYPE = 'application/x-www-form-urlencoded';
    const REQUIRED_ARGUMENTS_KEY = 'required';
    const OPTIONAL_ARGUMENTS_KEY = 'optional';
    const TOKEN_ARGUMENT_KEY = 'token';

    private $arguments = [
        'rtm.start' => [
            self::REQUIRED_ARGUMENTS_KEY => [
                self::TOKEN_ARGUMENT_KEY,
            ],
            self::OPTIONAL_ARGUMENTS_KEY => [
                'simple_latest',
                'no_unreads',
                'mpim_aware',
            ],
        ],
        'chat.postMessage' => [
            self::REQUIRED_ARGUMENTS_KEY => [
                self::TOKEN_ARGUMENT_KEY,
                'channel',
                'text',
            ],
            self::OPTIONAL_ARGUMENTS_KEY => [
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
            self::REQUIRED_ARGUMENTS_KEY => [
                'client_id',
                'client_secret',
                'code',
            ],
            self::OPTIONAL_ARGUMENTS_KEY => [
                'redirect_uri',
            ],
        ],
        'team.info' => [
            self::REQUIRED_ARGUMENTS_KEY => [
                self::TOKEN_ARGUMENT_KEY,
            ],
        ],
        'im.list' => [
            self::REQUIRED_ARGUMENTS_KEY => [
                self::TOKEN_ARGUMENT_KEY,
            ],
        ],
        'users.list' => [
            self::REQUIRED_ARGUMENTS_KEY => [
                self::TOKEN_ARGUMENT_KEY,
            ],
            self::OPTIONAL_ARGUMENTS_KEY => [
                'presence',
            ],
        ],
        'users.info' => [
            self::REQUIRED_ARGUMENTS_KEY => [
                self::TOKEN_ARGUMENT_KEY,
                'user',
            ],
        ],
    ];

    private $token;

    /**
     * ApiClient constructor.
     *
     * @param null $token
     */
    public function __construct($token = null)
    {
        $this->setToken($token);
    }

    /**
     * API CURL Call with post method.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function apiCall(string $method, array $arguments = [])
    {
        try {
            $requestBody = $this->prepareRequestBody($method, $arguments);
            $response = $this->sendRequest($method, $requestBody);

            return $this->processResponse($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $method
     * @param $requestBody
     *
     * @throws \Exception
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function sendRequest(string $method, $requestBody)
    {
        try {
            /** @noinspection PhpUndefinedClassInspection */
            $request = new Request(
                'POST',
                self::BASE_URL.$method,
                ['Content-Type' => self::CONTENT_TYPE],
                $requestBody
            );

            return $this->getClient()->send($request);
        } catch (\Exception $e) {
            throw new BotonomousException('Failed to send data to the Slack API: '.$e->getMessage());
        }
    }

    /**
     * @param $method
     * @param array $arguments
     *
     * @throws \Exception
     *
     * @return string
     */
    private function prepareRequestBody(string $method, array $arguments = [])
    {
        $arguments = array_merge($arguments, $this->getArgs());

        // check the required arguments are provided
        try {
            $this->validateRequiredArguments($method, $arguments);
        } catch (\Exception $e) {
            throw new BotonomousException('Missing required argument(s): '.$e->getMessage());
        }

        // filter unwanted arguments
        return http_build_query($this->filterArguments($method, $arguments));
    }

    /**
     * @param $response
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function processResponse(ResponseInterface $response)
    {
        $response = json_decode($response->getBody()->getContents(), true);

        if (!is_array($response)) {
            throw new BotonomousException('Failed to process response from the Slack API');
        }

        return $response;
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getArgs():array
    {
        return [
            self::TOKEN_ARGUMENT_KEY    => $this->getToken(),
            'username' => $this->getConfig()->get('botUsername'),
            'as_user'  => $this->getConfig()->get('asUser'),
            'icon_url' => $this->getConfig()->get('iconURL'),
        ];
    }

    /**
     * @param $arguments
     *
     * @return mixed
     */
    public function chatPostMessage(array $arguments)
    {
        return $this->apiCall('chat.postMessage', $arguments);
    }

    /**
     * @param $arguments
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function rtmStart(array $arguments)
    {
        return $this->apiCall('rtm.start', $arguments);
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
     * @return \Botonomous\AbstractBaseSlack|null|void
     */
    public function teamInfoAsObject()
    {
        $teamInfo = $this->teamInfo();

        if (empty($teamInfo)) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        // return as object
        return (new Team())->load($teamInfo);
    }

    /**
     * List all the Slack users in the team.
     *
     * @return array
     */
    public function usersList(): array
    {
        $result = $this->apiCall('users.list');

        if (!isset($result['members'])) {
            return [];
        }

        return $result['members'];
    }

    /**
     * Return a user by Slack user id.
     *
     * @param $arguments
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function userInfo(array $arguments)
    {
        $result = $this->apiCall('users.info', $arguments);

        if (!isset($result['user'])) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return $result['user'];
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
     * @return array
     */
    public function imListAsObject(): array
    {
        $imChannels = $this->imList();

        $imChannelObjects = [];
        if (empty($imChannels)) {
            return $imChannelObjects;
        }

        foreach ($imChannels as $imChannel) {
            $imChannelObjects[$imChannel['id']] = (new ImChannel())->load($imChannel);
        }

        return $imChannelObjects;
    }

    /**
     * @param $arguments
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function oauthAccess(array $arguments)
    {
        return $this->apiCall('oauth.access', $arguments);
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @throws \Exception
     *
     * @return bool
     */
    private function validateRequiredArguments(string $method, array $arguments)
    {
        $validArguments = $this->getArguments($method);

        if (empty($validArguments[self::REQUIRED_ARGUMENTS_KEY])) {
            return true;
        }

        foreach ($validArguments[self::REQUIRED_ARGUMENTS_KEY] as $argument) {
            if ($this->getArrayUtility()->arrayKeyValueExists($argument, $arguments) !== true) {
                throw new BotonomousException("{$argument} must be provided for {$method}");
            }
        }

        return true;
    }

    /**
     * @param null $method
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getArguments($method = null)
    {
        if ($method !== null) {
            if (!isset($this->arguments[$method])) {
                /* @noinspection PhpInconsistentReturnPointsInspection */
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
     * @param array $arguments
     *
     * @return array
     */
    public function filterArguments(string $method, array $arguments): array
    {
        $validArguments = $this->getArguments($method);

        if (empty($validArguments)) {
            return $arguments;
        }

        if (!isset($validArguments[self::OPTIONAL_ARGUMENTS_KEY])) {
            $validArguments[self::OPTIONAL_ARGUMENTS_KEY] = [];
        }

        $extractedArguments = array_merge($validArguments[self::REQUIRED_ARGUMENTS_KEY], $validArguments[self::OPTIONAL_ARGUMENTS_KEY]);

        return $this->getArrayUtility()->filterArray($arguments, $extractedArguments);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        // fall back to config
        if (empty($this->token)) {
            $this->setToken($this->getConfig()->get('botUserToken'));
        }

        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
