<?php

namespace Slackbot\client;

use Slackbot\Config;
use Slackbot\utility\LoggerUtility;

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
        $args = array_merge($args, $this->getArgs());
        $data = http_build_query($args);

        $connection = curl_init(self::BASE_URL.$method);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($connection);
        curl_close($connection);

        (new LoggerUtility())->logChat(__METHOD__.' '.$method, $result);

        // prettify the response
        return json_decode($result, true);
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
        return $this->apiCall('users.list')['members'];
    }
}
