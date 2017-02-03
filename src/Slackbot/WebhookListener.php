<?php

namespace Slackbot;

class WebhookListener extends BaseListener
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * listen.
     */
    public function listen()
    {
        $request = $this->extractRequest();

        if (empty($request)) {
            return;
        }

        // This is needed for Slash commands, otherwise timeout error is displayed
        $this->respondOK();

        $this->setRequest($request);
    }

    private function respondOK()
    {
        ob_start();
        echo('{"response_type": "in_channel", "text": ""}');
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Content-Type: application/json");
        header('Content-Length: '.ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();
    }

    /**
     * @return mixed|void
     */
    public function extractRequest()
    {
        $postRequest = filter_input_array(INPUT_POST);

        if (empty($postRequest)) {
            return;
        }

        return $postRequest;
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

        $expectedToken = $this->getConfig()->get(self::VERIFICATION_TOKEN);

        if (empty($expectedToken)) {
            throw new \Exception('Token must be set in the config');
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
