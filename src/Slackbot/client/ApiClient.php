<?php

namespace Slackbot\client;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Request;
use Slackbot\Config;
use Slackbot\Team;
use Slackbot\utility\StringUtility;

/**
 * Class ApiClient.
 */
class ApiClient
{
    const BASE_URL = 'https://slack.com/api/';

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
        try {
            /** @noinspection PhpUndefinedClassInspection */
            $request = new Request(
                'POST',
                self::BASE_URL.$method,
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                http_build_query(array_merge($args, $this->getArgs()))
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
            'channel'  => $config->get('channelName'),
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
}
