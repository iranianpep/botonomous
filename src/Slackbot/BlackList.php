<?php

namespace Slackbot;

use Slackbot\client\ApiClient;

class BlackList
{
    private $request;
    private $dictionary;
    private $apiClient;

    public function __construct($request)
    {
        $this->setRequest($request);
    }

    public function isBlackListed()
    {
        // currently isUsernameBlackListed is the only function, but later a new one might be added
        if ($this->isUsernameBlackListed() !== false) {
            return true;
        }

        if ($this->isUserIdBlackListed() !== false) {
            return true;
        }

        return false;
    }

    public function isUsernameBlackListed()
    {
        // get user name in the request
        $request = $this->getRequest();

        // currently if user_name is not set we do not check it
        if (!isset($request['user_name'])) {
            return false;
        }

        // user_name is set, load the blacklist to start checking
        $blacklist = $this->getDictionary()->get('blacklist');

        // currently if username is not set we do not check it
        if (!isset($blacklist['username'])) {
            return false;
        }

        if (in_array($request['user_name'], $blacklist['username'])) {
            return true;
        }

        return false;
    }

    public function isUserIdBlackListed()
    {
        // get user id in the request
        $request = $this->getRequest();

        // currently if user_id is not set we do not check it
        if (!isset($request['user_id'])) {
            return false;
        }

        // user_name is set, load the blacklist to start checking
        $blacklist = $this->getDictionary()->get('blacklist');

        // currently if username is not set we do not check it
        if (!isset($blacklist['userId'])) {
            return false;
        }

        if (in_array($request['user_id'], $blacklist['userId'])) {
            return true;
        }

        return false;
    }

    public function isEmailBlackListed()
    {
        // get user id in the request
        $request = $this->getRequest();

        // currently if user_id is not set we do not check it
        if (!isset($request['user_id'])) {
            return false;
        }

        // email normally does not exist in the request. Get it by user_id. For this users:read and users:read.email are needed
        $userInfo = $this->getApiClient()->userInfo(['user' => $request['user_id']]);

        if (empty($userInfo)) {
            // Could not find the user in the team - This is weird! There might be some issue with Access token, but block the access
            return true;
        }

        // user_name is set, load the blacklist to start checking
        $blacklist = $this->getDictionary()->get('blacklist');

        // currently if username is not set we do not check it
        if (!isset($blacklist['userEmail'])) {
            return false;
        }

        if (in_array($userInfo['profile']['email'], $blacklist['userEmail'])) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary()
    {
        if (!isset($this->dictionary)) {
            $this->setDictionary(new Dictionary());
        }

        return $this->dictionary;
    }

    /**
     * @param Dictionary $dictionary
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        if (!isset($this->apiClient)) {
            $this->setApiClient(new ApiClient());
        }

        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
}
