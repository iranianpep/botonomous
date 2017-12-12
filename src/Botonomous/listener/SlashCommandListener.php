<?php

namespace Botonomous\listener;

class SlashCommandListener extends AbstractBaseListener
{
    const VERIFICATION_TOKEN = 'verificationToken';
    const MISSING_TOKEN_MESSAGE = 'Token is missing';
    const MISSING_TOKEN_CONFIG_MESSAGE = 'Token must be set in the config';

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
    public function verifyOrigin(): array
    {
        $token = $this->getRequest('token');

        if (empty($token)) {
            return [
                'success' => false,
                'message' => self::MISSING_TOKEN_MESSAGE,
            ];
        }

        $expectedToken = $this->getConfig()->get(self::VERIFICATION_TOKEN);

        if (empty($expectedToken)) {
            throw new \Exception(self::MISSING_TOKEN_CONFIG_MESSAGE);
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
    public function isThisBot(): bool
    {
        $userId = $this->getRequest('user_id');
        $username = $this->getRequest('user_name');

        return ($userId == 'USLACKBOT' || $username == 'slackbot') ? true : false;
    }

    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->getRequest('channel_id');
    }
}
