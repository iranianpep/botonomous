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
        if ($this->isUsernameBlackListed() !== false) {
            return true;
        }

        if ($this->isUserIdBlackListed() !== false) {
            return true;
        }

        if ($this->isEmailBlackListed() !== false) {
            return true;
        }

        return false;
    }

    private function findInListByRequestKey($requestKey, $listKey)
    {
        // get request
        $request = $this->getRequest();

        // currently if request key is not set we do not check it
        if (!isset($request[$requestKey])) {
            return false;
        }

        // request key is set, load the blacklist to start checking
        $blacklist = $this->getDictionary()->get('blacklist');

        // currently if list key is not set we do not check it
        if (!isset($blacklist[$listKey])) {
            return false;
        }

        if (in_array($request[$requestKey], $blacklist[$listKey])) {
            return true;
        }

        return false;
    }

    public function isUsernameBlackListed()
    {
        return $this->findInListByRequestKey('user_name', 'username');
    }

    public function isUserIdBlackListed()
    {
        return $this->findInListByRequestKey('user_id', 'userId');
    }

    public function isEmailBlackListed()
    {
        // get user id in the request
        $request = $this->getRequest();

        // currently if user_id is not set we do not check it
        if (!isset($request['user_id'])) {
            return false;
        }

        /**
         * email normally does not exist in the request.
         * Get it by user_id. For this users:read and users:read.email are needed.
         */
        $userInfo = $this->getApiClient()->userInfo(['user' => $request['user_id']]);

        if (empty($userInfo)) {
            /*
             * Could not find the user in the team
             * Probably there might be some issue with Access token and reading user info but block the access
             */
            return false;
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
