<?php

namespace Slackbot\client;

use Slackbot\Config;

class ApiClient
{
    const BASE_URL = 'https://slack.com/api/';

    /**
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

        return $result;
    }

    /**
     * @param $args
     * @return mixed
     */
    public function postMessage($args)
    {
        return $this->apiCall('chat.postMessage', $args);
    }
}
