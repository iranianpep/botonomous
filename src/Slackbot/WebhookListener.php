<?php

namespace Slackbot;

class WebhookListener extends BaseListener
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * @return mixed|void
     */
    public function listen()
    {
        // This is needed for Slash commands, otherwise timeout error is displayed
        $this->respondOK();

        $request = $this->extractRequest();

        if (empty($request)) {
            return;
        }


        $this->setRequest($request);

        if ($this->isThisBot() !== false) {
            return;
        }

        return $request;
    }

    /**
     * @return mixed
     */
    public function extractRequest()
    {
        $postRequest = $this->getRequestUtility()->getPost();

        if (empty($postRequest)) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
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
        $username = $this->getRequest('user_name');

        return ($userId == 'USLACKBOT' || $username == 'slackbot') ? true : false;
    }
}
