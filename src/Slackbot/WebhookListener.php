<?php

namespace Slackbot;

class WebhookListener extends BaseListener
{
    public function listen()
    {
        $request = $this->extractRequest();

        if (empty($request)) {
            return;
        }

        $this->setRequest($request);
    }

    public function extractRequest()
    {
        if (empty($_POST)) {
            return;
        }

        return json_decode($_POST, true);
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function verifyOrigin()
    {
        $token = $this->getRequest('token');

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token is missing',
            ];
        }

        $expectedToken = $this->getConfig()->get('outgoingWebhookToken');

        if (empty($expectedToken)) {
            throw new \Exception('Token must be provided');
        }

        if ($token === $expectedToken) {
            return [
                'success' => true,
                'message' => 'Awesome!',
            ];
        }

        return [
            'success' => false,
            'message' => 'Token is not valid',
        ];
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isThisBot()
    {
        $userId = $this->getRequest('user_id');

        if (empty($userId)) {
            throw new \Exception('Bot user id must be provided');
        }

        $username = $this->getRequest('user_name');

        if (empty($username)) {
            throw new \Exception('Bot user name must be provided');
        }

        return ($userId == 'USLACKBOT' || $username == 'slackbot') ? true : false;
    }
}
