<?php

namespace Slackbot\client;

use Slackbot\Config;
use Slackbot\utility\LoggerUtility;

class ApiClient
{
    const BASE_URL = 'https://slack.com/api/';

    /**
     * API Call
     *
     * @param $method
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    public function apiCall($method, array $args = [])
    {
        $ch = curl_init(self::BASE_URL . $method);
        
        $config = new Config();
        $args['token'] = $config->get('apiToken');
        $args['channel'] = $config->get('channelName');
        $args['username'] = $config->get('botUsername');
        $args['as_user'] = false;
        $args['icon_url'] = $config->get('iconURL');
        
        $data = http_build_query($args);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        (new LoggerUtility())->logChat(__METHOD__ . ' ' . $method, $result);

        // prettify the response
        $result = json_decode($result, true);

        // TODO check in the response "ok":true before getting members list

        return $result;
    }

    /**
     * @param $args
     * @return mixed
     */
    public function chatPostMessage($args)
    {
        return $this->apiCall('chat.postMessage', $args);
    }

    /**
     * List all the Slack users in the team
     * @return array
     */
    public function usersList()
    {
        return $this->apiCall('users.list')['members'];
    }
}
