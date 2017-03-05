<?php

namespace Slackbot;

use Slackbot\client\ApiClient;

abstract class AbstractAccessList
{
    /**
     * Dependencies
     */
    private $request;
    private $dictionary;
    private $apiClient;

    protected function getAccessControlList()
    {
        return $this->getDictionary()->get('access-control');
    }

    protected function getSubAccessControlList($sublistKey)
    {
        $list = $this->getAccessControlList();

        if (!isset($list[$sublistKey])) {
            return;
        }

        return $list[$sublistKey];
    }

    protected function findInListByRequestKey($requestKey, $listKey)
    {
        // get request
        $request = $this->getRequest();

        /**
         * load the relevant list to start checking
         * The list name is the called class name e.g. WhiteList in lowercase.
         */
        $list = $this->getSubAccessControlList(strtolower(get_called_class()));

        if ($list === null) {
            return;
        }

        // currently if list key is not set we do not check it
        if (!isset($list[$listKey])) {
            return;
        }

        if (in_array($request[$requestKey], $list[$listKey])) {
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

    public function getSlackUserInfo()
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

        return $userInfo;
    }
}
