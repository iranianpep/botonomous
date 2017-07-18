<?php

namespace Botonomous;

use Botonomous\client\ApiClient;
use Botonomous\utility\ClassUtility;

abstract class AbstractAccessList
{
    /**
     * Dependencies.
     */
    private $request;
    private $dictionary;
    private $apiClient;
    private $classUtility;

    /**
     * @return mixed
     */
    protected function getAccessControlList()
    {
        return $this->getDictionary()->get('access-control');
    }

    /**
     * @param $sublistKey
     *
     * @return mixed
     */
    protected function getSubAccessControlList($sublistKey)
    {
        $list = $this->getAccessControlList();

        if (!isset($list[$sublistKey])) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return $list[$sublistKey];
    }

    /**
     * @param array $list
     *
     * @return bool
     */
    protected function isEmailInList(array $list)
    {
        // get user info
        $userInfo = $this->getSlackUserInfo();

        return !empty($userInfo) && in_array($userInfo['profile']['email'], $list['userEmail']);
    }

    /**
     * Check if email is white listed or black listed
     * If userEmail list is not set, return true for whitelist and false for blacklist.
     *
     * @return bool
     */
    protected function checkEmail()
    {
        // load the relevant list based on the class name e.g. BlackList or WhiteList
        $list = $this->getSubAccessControlList($this->getShortClassName());

        if (!isset($list['userEmail'])) {
            // if list is not set do not check it
            return $this->getShortClassName() === 'whitelist' ? true : false;
        }

        return $this->isEmailInList($list);
    }

    /**
     * @param string $requestKey
     * @param $listKey
     * @param string $subListKey
     *
     * @return bool|null
     */
    protected function findInListByRequestKey($requestKey, $listKey, $subListKey)
    {
        /**
         * load the relevant list to start checking
         * The list name is the called class name e.g. WhiteList in lowercase.
         */
        $list = $this->getSubAccessControlList($listKey);

        // currently if list key is not set we do not check it
        if ($list === null || !isset($list[$subListKey])) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return in_array($this->getRequest()[$requestKey], $list[$subListKey]);
    }

    /**
     * @return mixed
     */
    protected function getShortClassName()
    {
        return $this->getClassUtility()->extractClassNameFromFullName(strtolower(get_called_class()));
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

    /**
     * @return array|bool
     */
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

    /**
     * @return ClassUtility
     */
    public function getClassUtility()
    {
        if (!isset($this->classUtility)) {
            $this->setClassUtility(new ClassUtility());
        }

        return $this->classUtility;
    }

    /**
     * @param ClassUtility $classUtility
     */
    public function setClassUtility(ClassUtility $classUtility)
    {
        $this->classUtility = $classUtility;
    }
}
