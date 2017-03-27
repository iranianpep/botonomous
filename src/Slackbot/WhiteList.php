<?php

namespace Slackbot;

class WhiteList extends AbstractAccessList
{
    /**
     * WhiteList constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->setRequest($request);
    }

    /**
     * @return bool
     */
    public function isWhiteListed()
    {
        $usernameCheck = true;
        $userIdCheck = true;
        $userEmailCheck = true;

        if ($this->isUsernameWhiteListed() === false) {
            $usernameCheck = false;
        }

        if ($this->isUserIdWhiteListed() === false) {
            $userIdCheck = false;
        }

        if ($this->isEmailWhiteListed() === false) {
            $userEmailCheck = false;
        }

        if ($usernameCheck === true && $userIdCheck === true && $userEmailCheck === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isUsernameWhiteListed()
    {
        if (empty($this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username'))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isUserIdWhiteListed()
    {
        if (empty($this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId'))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     *
     * @return mixed
     */
    public function isEmailWhiteListed()
    {
        // user_name is set, load the blacklist to start checking
        $list = $this->getSubAccessControlList($this->getShortClassName());

        // currently if list is not set we do not check it
        if (!isset($list['userEmail'])) {
            return true;
        }

        // get user info
        $userInfo = $this->getSlackUserInfo();

        if (empty($userInfo)) {
            return false;
        }

        if (in_array($userInfo['profile']['email'], $list['userEmail'])) {
            return true;
        }

        return false;
    }
}
