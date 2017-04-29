<?php

namespace Slackbot\listener;

class SlashCommandListener extends AbstractBaseListener
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * @return mixed
     */
    public function listen()
    {
        // This is needed otherwise timeout error is displayed
        $this->respondOK();

        $request = $this->extractRequest();

        if (empty($request)) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        $this->setRequest($request);

        if ($this->isThisBot() !== false) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
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
