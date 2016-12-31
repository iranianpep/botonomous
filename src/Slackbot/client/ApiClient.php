<?php

namespace Slackbot\client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Slackbot\Config;

/**
 * Class ApiClient.
 */
class ApiClient
{
    const BASE_URL = 'https://slack.com/api/';

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
            $request = new Request(
                'POST',
                self::BASE_URL.$method,
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                http_build_query(array_merge($args, $this->getArgs()))
            );

            $response = (new Client())->send($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to send data to the Slack API: '.$e->getMessage());
        }

        try {
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \Exception('Failed to process response from the Slack API: '.$e->getMessage());
        }
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

        return $this->apiCall('users.list')['members'];
    }
}
